<?php
/**
 * @Date: 2024/3/20 16:20
 */
declare(strict_types=1);
namespace app\common\jobs;

use app\common\constants\CacheKeyConstant;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\ProductHelper;
use app\common\services\common\ConsoleLogService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderService;
use app\common\services\ProductCacheService;
use think\facade\Log;
use think\queue\Job;
use think\Exception;

class CheckOrderJob
{
    public function fire(Job $job, $data)
    {
        try {
            if ($this->checkOrder($data)) {
                $job->delete();
            } else {
                if ($job->attempts() > 5) {
                    $job->failed();
                } else {
                    $job->release(1);
                }
            }
        } catch (\Exception $e) {
            $job->failed($e);
        }
    }

    public function checkOrder($data)
    {
        if(empty($data['pid']) || empty($data['price'])){
            return true;
        }
        $pid = $data['pid'];
        $productNowPrice = $data['price'];

        $logService = app(ConsoleLogService::class);
        //获取全部持仓
        $orderService = app(OrderService::class);
        $productService = app(ProductCacheService::class);
        $memberService = app(MemberOrderService::class);
        //产品详细
        $productDetail = $productService->getProductDetailCache($pid);
        $orderList = $orderService->dao->model
            ->where('pid',$pid)
            ->where('ostaus',0)
            ->where(function ($query){
                $query->whereOr([
                    [
                        ["surplus",">",0]
                    ],[
                        ["loss",">",0]
                    ],[
                        ["trigger_price",">",0]
                    ]
                ]);
            })
            ->select()->toArray();


        foreach ($orderList as $item){
            $isBongPing = false;

            //验证休市
            $isOpen = ProductHelper::checkIsOpen($productDetail['open_time']);
            if (!empty($productDetail['open_time']) && !$isOpen) {
                continue;
            }
            if ($productDetail['status']['value'] == 0) {
                continue;
            }

            //平仓方式 sell_type
            $sellType = '';
            //买跌 达到止盈
            if ( $item['ostyle']['value'] == 1 && (!empty($item['surplus']) && $productNowPrice <= $item['buyprice'] && $productNowPrice <= $item['surplus'])
            ) {
                $logService->create("【强平】用户：{$item['member_id']}，到达到买跌止盈",true);
                $isBongPing = true;
                $sellType = SellTypeEnum::SURPLUS;
                //买跌 止损线
            }else if (  $item['ostyle']['value'] == 1 && (!empty($item['loss']) && $productNowPrice >= $item['buyprice'] && $productNowPrice >= $item['loss']) ) {
                $logService->create("【强平】用户：{$item['member_id']}，到达止买跌损线",true);
                $isBongPing = true;
                $sellType = SellTypeEnum::LOSS;
                //买涨 止盈
            } else if ( $item['ostyle']['value'] == 2 && (!empty($item['surplus']) && $productNowPrice >= $item['buyprice'] && $productNowPrice >= $item['surplus']) ) {
                $logService->create("【强平】用户：{$item['member_id']}，到达到买涨止盈",true);
                $isBongPing = true;
                $sellType = SellTypeEnum::SURPLUS;
                //买涨 止损线
            } elseif (
                $item['ostyle']['value'] == 2
                && (!empty($item['loss'])  && $productNowPrice <= $item['buyprice'] && $productNowPrice <= $item['loss'])
            ) {
                $logService->create("【强平】用户：{$item['member_id']}，到达止买涨损线",true);
                $isBongPing = true;
                $sellType = SellTypeEnum::LOSS;
            }

            // 触发标记线
            if (!empty($item['mark_price']) && !empty($item['trigger_price'])) {
                if($item['mark_price'] >= $item['trigger_price'] && $productNowPrice > $item['mark_price'] ){
                    $isBongPing = true;
                    $sellType = SellTypeEnum::MARK;
                }elseif ($item['mark_price'] <=  $item['trigger_price'] && $item['mark_price'] > $productNowPrice){
                    $isBongPing = true;
                    $sellType = SellTypeEnum::MARK;
                }
            }

            //获取当前交易及钱包详细
            $memberTrade = $memberService->getMemberTradeInfo($item['member_id']);

            //平仓
            if ($isBongPing) {
                $logService->create("用户：{$item['member_id']}平仓：{$item['id']}",true);
                $memberService->pingCangZhiDing($item['member_id'], $item['id'],$sellType,[
                    'user_jiao_info' =>$memberTrade
                ]);
            }
        }
        return true;
    }

    public function failed($data)
    {
        // 记录任务执行失败日志
        Log::record('jobs: ' . json_encode($data), 'error');
    }
}