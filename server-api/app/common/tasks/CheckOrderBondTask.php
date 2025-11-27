<?php
/**
 * @Date: 2025/7/4 10:56
 */
declare(strict_types=1);
namespace app\common\tasks;

use app\common\constants\CacheKeyConstant;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use app\common\services\member\MemberCoinService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderService;
use app\common\services\ProductCacheService;
use EasySwoole\Component\Process\AbstractProcess;
use Swoole\Coroutine;

class CheckOrderBondTask extends AbstractProcess
{
    public mixed $cacheService = null;

    public mixed $logService = null;


    public function run($arg)
    {
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        // TODO: Implement run() method.
        $this->logService->create("【强平】检测程序开始", true);
        $this->addTick(1000, function () {
            $this->createCheck();
        });
    }

    public function createCheck()
    {
//        $this->logService->create("【强平】检测程序继续", true);
        go(function () {
            //获取全部持仓
            $orderService = app(OrderService::class);
            $productService = app(ProductCacheService::class);
            $memberService = app(MemberOrderService::class);
            $orderList = $orderService->dao->model->where('ostaus',0)->select()->toArray();
            //循环处理订单

            foreach ($orderList as $item){
                $isBongPing = false;
                //获取产品价格
                $productNowPrice =  $this->cacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['pid']);
                //行情价格获取失败!请稍后重试!
                if (empty($productNowPrice)){
                    continue;
                }
                //产品详细
                $productDetail = $productService->getProductDetailCache($item['pid']);

                //验证休市
                $isOpen = ProductHelper::checkIsOpen($productDetail['open_time']);
                if (!empty($productDetail['open_time']) && !$isOpen) {
                    continue;
                }
                if ($productDetail['status']['value'] == 0) {
                    continue;
                }

                //达到强平时间
                $isYesterDayClose = false;
                if ($productDetail['yesterday_close'] == 1 && ProductHelper::checkIsNightClose($productDetail['yesterday_close_time'])) {
                    $isYesterDayClose = true;
                }

                //平仓方式 sell_type
                $sellType = '';
                //买跌 达到止盈
                if ( $item['ostyle']['value'] == 1 && (!empty($item['surplus']) && $productNowPrice <= $item['buyprice'] && $productNowPrice <= $item['surplus'])
                ) {
                    $this->logService->create("【强平】用户：{$item['member_id']}，到达到买跌止盈",true);
                    $isBongPing = true;
                    $sellType = SellTypeEnum::SURPLUS;
                    //买跌 止损线
                }else if (  $item['ostyle']['value'] == 1 && (!empty($item['loss']) && $productNowPrice >= $item['buyprice'] && $productNowPrice >= $item['loss']) ) {
                    $this->logService->create("【强平】用户：{$item['member_id']}，到达止买跌损线",true);
                    $isBongPing = true;
                    $sellType = SellTypeEnum::LOSS;
                    //买涨 止盈
                } else if ( $item['ostyle']['value'] == 2 && (!empty($item['surplus']) && $productNowPrice >= $item['buyprice'] && $productNowPrice >= $item['surplus']) ) {
                    $this->logService->create("【强平】用户：{$item['member_id']}，到达到买涨止盈",true);
                    $isBongPing = true;
                    $sellType = SellTypeEnum::SURPLUS;
                    //买涨 止损线
                } elseif (
                    $item['ostyle']['value'] == 2
                    && (!empty($item['loss'])  && $productNowPrice <= $item['buyprice'] && $productNowPrice <= $item['loss'])
                ) {
                    $this->logService->create("【强平】用户：{$item['member_id']}，到达止买涨损线",true);
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

                //隔夜-保证金比例过低
                if (!$isBongPing && $isYesterDayClose) {
                    if ($memberTrade['count'] !== 0 && $memberTrade['baozhengjin_rate'] < 500) {
                        $this->logService->create("【强平】用户：{$item['member_id']}，隔夜-保证金比例过低",true);
                        $isBongPing = true;
                        $sellType = SellTypeEnum::OVERNIGHT;
                    }
                }

                if (!$isBongPing ) {
                    if ($memberTrade['count'] !== 0 && $memberTrade['baozhengjin_rate'] < 40) {
                        $this->logService->create("用户bond：{$item['member_id']}平仓：{$item['id']}".json_encode($memberTrade,JSON_UNESCAPED_UNICODE),true);
                        $sellType = SellTypeEnum::BOND;
                        $memberService->pingCangMax($memberTrade['order_hold'],$item['member_id'],$sellType,[
                            'user_jiao_info' =>$memberTrade
                        ]);
                    }
                }

                //平仓
                if ($isBongPing) {
                    $this->logService->create("用户：{$item['member_id']}平仓：{$item['id']}",true);
                    $memberService->pingCangZhiDing($item['member_id'], $item['id'],$sellType,[
                        'user_jiao_info' =>$memberTrade
                    ]);
                }
            }

        });
    }
}