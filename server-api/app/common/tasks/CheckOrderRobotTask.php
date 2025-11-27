<?php
/**
 * @Date: 2025/6/30 21:19
 */
declare(strict_types=1);
namespace app\common\tasks;

use app\common\constants\CacheKeyConstant;
use app\common\constants\StringConstant;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderRobotService;
use app\websocket\services\PushMessageService;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\TableManager;
use Swoole\Coroutine;

class CheckOrderRobotTask extends AbstractProcess
{
    public mixed $cacheService = null;

    public mixed $logService = null;


    public function run($arg)
    {
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        // TODO: Implement run() method.
        $this->logService->create("【挂单】检测程序开始", true);
        $this->addTick(1000, function () {
            $this->createCheck();
        });
    }

    public function createCheck()
    {
        go(function () {
            //获取挂单
            $orderRobotService = app(OrderRobotService::class);
            $robotList = $orderRobotService->getAllOrderRobot();
            $memberService = app(MemberOrderService::class);
            $successCount = 0;
            $failCount = 0;
            $count = count($robotList);
            foreach ($robotList as $key => $item) {
                //获取产品价格
                $productNowPrice =  $this->cacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['pid']);

                //行情价格获取失败!请稍后重试!
                if (empty($productNowPrice)){
                    continue;
                }
                //是否达到成交价格
                $isStatus = false;
                //如果标记大于当时价 则现在大于标记价
                // //现在价格 > 标记价格额 满足价格
                if (!empty($item['mark_price']) && $item['buyprice'] >= $item['mark_price'] && $productNowPrice >= $item['buyprice']) {
                    $isStatus = true;
                }elseif (!empty($item['mark_price']) && $item['buyprice'] <= $item['mark_price'] && $productNowPrice <= $item['buyprice']) {
                    $isStatus = true;
                }
                if ($isStatus) {
                    $res = $memberService->createOrder(
                        $item['member_id'],
                        $item['ostyle']['value'],
                        $item['order_price'],
                        $productNowPrice,
                        $item['onumber'],
                        $item['surplus'],
                        $item['loss'],
                        $item['pid']
                    );
                    if(!empty($res['id'])){
                        $orderRobotService->dao->model->where('id',$item['id'])->update([
                            'status'=>1,
                            'order_id'=>$res['id'],
                            'deal_time'=>time(),
                            'remark'=>'挂单成功'
                        ]);
                        $successCount++;
                    }elseif(!$res && $memberService->hasError()){
                        $orderRobotService->dao->model->where('id',$item['id'])->update([
                            'status'=>2,
                            'close_time'=>time(),
                            'remark'=>'挂单失败'.$memberService->getError()
                        ]);
                        $failCount++;
                    }
                }
            }
//            $count > 0 && $this->logService->create("【挂单】总挂单".$count."，成功挂单".$successCount."个，失败".$failCount."个",true);
        });
    }
}