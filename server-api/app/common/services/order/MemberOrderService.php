<?php
/**
 * @Date: 2025/6/27 17:02
 */
declare(strict_types=1);
namespace app\common\services\order;

use app\common\constants\CacheKeyConstant;
use app\common\dao\order\OrderDao;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\agent\AgentService;
use app\common\services\BaseService;
use app\common\services\common\CacheService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\WaterService;
use app\common\services\follow\PersonService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use app\common\services\ProductCacheService;
use think\facade\Db;
use think\facade\Queue;

/**
 * 订单
 * Class MemberOrderService
 */
class MemberOrderService extends BaseService
{

    /**
     * OrderService constructor.
     * @param OrderDao $dao
     */
    public function __construct(OrderDao $dao)
    {
        $this->dao = $dao;
    }


    /*
     * 根据用户获取当前持仓订单
     */
    public function getChiCangOrdersByMemberId($memberId): array
    {
        $holdList = $this->dao->model->where('member_id', $memberId)->where('ostaus', 0)->order([
            'create_time' => 'desc'
        ])->select()->toArray();
        $marketCacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $productCacheService = app(ProductCacheService::class);
        foreach ($holdList as &$item){
            //获取产品价格
            $productNotPrice = $marketCacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['pid']);
            $productDetail = $productCacheService->getProductDetailCache($item['pid']);
            //空价格
            if(empty($productNotPrice)){
                $productNotPrice = $productDetail['price'];
            }
            //默认买涨
            if($productDetail['number'] != 0){
                $item['yingkui'] = ProductHelper::orderProfit($productNotPrice, $productDetail['number'], $productDetail['money'], $item['buyprice'], $item['onumber']);
            }else{
                $item['yingkui'] = 0;
            }
            //如果买跌
            $item['ostyle']['value'] == 1 && $item['yingkui'] = $item['yingkui'] * -1;
            $item['yingkui'] = round($item['yingkui'], 2);

            $item['product_money'] = $productDetail['money'];
            $item['product_number'] = $productDetail['number'];
            $item['product_decimal'] = $productDetail['decimal'];

            //持仓订单现价
            $item['sellprice'] = $productNotPrice;

            $moneyDefaultRate = ProductHelper::moneyRate();
            //本单盈亏
            $item['yingkui_t'] = round($item['yingkui'] * $moneyDefaultRate, 0);

            //计算止盈、止损，硬性指标、单点盈亏
            //BP1申买价  SP1申卖价
            //买跌（卖出）
            if ($item['ostyle']['value'] ){
                $sunPrice = $productNotPrice + ($productDetail['number'] * $productDetail['money']);
                $yingPrice = $productNotPrice - ($productDetail['number'] * $productDetail['money']);
            } else {
                $sunPrice = $productNotPrice - ($productDetail['number'] * $productDetail['money']);
                $yingPrice = $productNotPrice + ($productDetail['number'] * $productDetail['money']);
            }
            $item['sun_price'] = $sunPrice;
            $item['ying_price'] = $yingPrice;
        }
        return $holdList;
    }

    /*
     * 获取用户交易信息
     */
    public function getMemberTradeInfo($memberId){
        $resData = [
            'count' => 0,
        ];
        try {
            //获取持仓订单
            $holdList = $this->getChiCangOrdersByMemberId($memberId);
            //获取挂单订单
            $robotList = app(OrderRobotService::class)->getOrderRobotListByMemberId($memberId);
            $resData['count'] = count($holdList);
            $resData['order_hold'] = $holdList;

            $yingKuiSum = 0;
            $payChooseSum = 0;
            $feeSum = 0;
            foreach ($holdList as $item) {
                //盈亏统计
                $yingKuiSum += $item['yingkui_t'] ?? 0;
                //保证金统计
                $payChooseSum += $item['fee'] ?? 0;
                //手续费
                $feeSum += $item['sx_fee'] ?? 0;
            }
            foreach ($robotList as $item) {
                //保证金统计
                $payChooseSum += $item['order_price'] ?? 0;
            }
            if($feeSum === 0 &&  $payChooseSum === 0 && $yingKuiSum === 0){
                $resData['count'] = 0;
            }

            $memberCoinService = app(MemberCoinService::class);
            $memberCoin = $memberCoinService->dao->model->where('member_id', $memberId)->find();
            $resData = array_merge($memberCoin->toArray(),$resData);
            $memberBalance = $memberCoin['balance'] ?? 0;
            //余额
            $resData['balance'] = StringHelper::number2($memberBalance);

            $resData['yingkui_sum'] = StringHelper::number2($yingKuiSum - $feeSum);
            $upMemberCoinData = [];
            if ($memberCoin['yingkui'] != $resData['yingkui_sum']) {
                $upMemberCoinData['yingkui'] = $resData['yingkui_sum'];
            }
            //占用保证金
            $resData['baozhengjin_sum'] = StringHelper::number2($payChooseSum);
            //可用金额
            $resData['money_keyong'] = StringHelper::number2($memberBalance - $payChooseSum + (float)$resData['yingkui_sum']);

            if ($memberCoin['keyong'] != $resData['money_keyong']) {
                $upMemberCoinData['keyong'] = $resData['money_keyong'];
            }
            //动态权益
            $resData['money_quanyi'] = StringHelper::number2( (float)$memberBalance + (float)$resData['yingkui_sum'] + (float)$resData['baozhengjin_sum']);

            //净值
            $resData['jingzhi'] = StringHelper::number2($memberBalance +  (float)$resData['yingkui_sum']);

            if ($memberCoin['jingzhi'] != $resData['jingzhi']) {
                $upMemberCoinData['jingzhi'] = $resData['jingzhi'];
            }
            //保证比例
            if ($resData['baozhengjin_sum'] > 0) {
                $resData['baozhengjin_rate'] = StringHelper::number2((float)$resData['jingzhi'] / (float)$resData['baozhengjin_sum'] * 100);
            } else {
                $resData['baozhengjin_rate'] = 0;
            }

            if ($upMemberCoinData) {
                $memberCoinService->dao->model->where('member_id',$memberId)->update($upMemberCoinData);
            }

            return $resData;
        }catch (\Exception $e){
            Db::connect()->close();
            return $resData;
        }
    }



    /**
     * 下单
     */
    public function createOrder($memberId,$orderType,$orderPrice,$createPrice,$oNumber,$surplus,$loss,$orderPid,$minute=0): bool| array
    {

        //$orderPrice 是保证金
        $createTime = time();
        //下单时间变更为当前时间

        if (empty($orderType)){
            $this->error = '参数错误!';
            return false;
        }

        //产品详细
        $productDetail = app(ProductCacheService::class)->getProductDetailCache($orderPid);
        if(empty($productDetail)){
            $this->error = '产品不存在!';
            return false;
        }

        //验证是否开市
        if(!empty($productCache['open_time']) && !ProductHelper::checkIsOpen($productDetail["open_time"],$minute)){
            $this->error = '休市不可开仓!';
            return false;
        }

        //验证状态
        if($productDetail['status']['value'] != 1){
            $this->error = '产品已关闭!';
            return false;
        }

        $memberService = app(\app\api\services\member\MemberService::class);
        $member = $memberService->dao->model->with(['coin'])->where('id', $memberId)->find();
        //验证用户是否被冻结
        if(empty($member)){
            $this->error = "您的账户不存在！";
            return false;
        }
        if($member['status']['value'] != 1){
            $this->error = "您的账户已被冻结";
            return false;
        }

        if (!empty($productDetail['buy_min']) && $productDetail['buy_min'] != 0 && $oNumber < $productDetail['buy_min'] ){
            $this->error = "开仓最低".$productDetail['buy_min']."手!请重新开仓";
            return false;
        }

        if (!empty($productDetail['buy_max']) && $productDetail['buy_min'] != 0 && $oNumber > $productDetail['buy_max'] ){
            $this->error = "开仓最大".$productDetail['buy_max']."手!请重新开仓";
            return false;
        }

        $decimal = $productDetail['decimal'];

        //处理止盈止损价格位数
        $surplus = empty($surplus) ? "" : round((float)$surplus,$decimal);
        $loss = empty($loss) ? "" : round((float)$loss,$decimal);

        $productNowPrice = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->get(CacheKeyConstant::PRODUCT_PRICE.":".$orderPid);

        if (empty($productNowPrice)){
//            $this->error = "行情价格获取失败!请稍后重试!";
            $this->error = "行情休市!请稍后重试!";
            return false;
        }
        //处理点差
//        dump($orderType,$productNowPrice);
//        if(!empty($productDetail["spread"]) && $productDetail["spread"] > 0){
//            if($orderType == 1){
//                $productNowPrice = sprintf("%.{$decimal}f",$productNowPrice - (float)$productDetail["spread"]);
//            }else{
//                $productNowPrice = sprintf("%.{$decimal}f",$productNowPrice +  (float)$productDetail["spread"]);
//            }
//        }
//        dump($productNowPrice,$productDetail["spread"]);die;
        //开仓价格
        //$buyPrice = $createPrice;
        $buyPrice = $productNowPrice;

        //做空 卖出 买跌
        if ($orderType == 1){
            //现价单
            if($loss && $loss < $buyPrice){
                $this->error = "止损价不能低于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus > $buyPrice){
                $this->error = "止盈价不能高于{$buyPrice}";
                return false;
            }
            //做多 买入
        }else{
            //现价单
            if($loss && $loss > $buyPrice){
                $this->error = "止损价不能高于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus < $buyPrice){
                $this->error = "止盈价不能低于{$buyPrice}";
                return false;
            }
        }
//        sleep(rand(1,2));
        //保证金
        $productPayChoose = $productDetail['pay_choose'];

        if($productPayChoose == 0 ) {
            $this->error = '该产品未设置保证金!暂时不能开仓！';
            return false;
        }
        //保证金重新计算赋值
        $orderPrice = $productPayChoose * $oNumber;

        //持仓限制
        if($orderPrice > sysconf('order_max_price')){
            $this->error = '单笔持仓最大为'.sysconf('order_max_price').'！';
            return false;
        }

        $orderFeeData = ProductHelper::getProductCreateOrderFee($member,$productDetail,$oNumber);
        $agentCommissions = $orderFeeData['agent_commissions'];

        $ratioFeeSum = array_sum(array_map(function($val){return $val['money'];}, $agentCommissions));

        //获取当前交易及钱包详细
        $memberTrade = app(MemberOrderService::class)->getMemberTradeInfo($memberId);

        $moneyDefaultRate = ProductHelper::moneyRate();
        if ($memberTrade['money_keyong'] < $orderPrice * $moneyDefaultRate){
            $this->error = "您得余额不足，请充值!";
            return false;
        }

        //验证金额 20 ~ 5000
        if($orderPrice * $moneyDefaultRate < sysconf('order_min_price') || $orderPrice * $moneyDefaultRate >  sysconf('order_max_price')){
            $this->error  = '抱歉！单笔持仓在'.sysconf('order_min_price').'~'. sysconf('order_max_price').'之间！';
            return false;
        }

        $sxFee =  (2 * $oNumber);

        if ($member['agent_id'] != 0){
            $sxFee = $ratioFeeSum;
        }

        //滑点
        if(!$member['moni']){
            $memberSlippage = $member['slippage'] ?? null;
            $slippage = $memberService->getMemberSlippage($orderPid,$memberSlippage,true);
            if(!empty($slippage)){
                $buyPrice = ProductHelper::setMemberProHuaDian($orderType,$buyPrice,$slippage);
            }else{
                $buyPrice = ProductHelper::setProHuaDian($orderType,$buyPrice,$productDetail['buy_slippage']);
            }
        }


        //建仓
        $orderData['orderno']= StringHelper::createOrderNo();
        $orderData['member_id'] = $memberId;
        $orderData['agent_id'] = $member['agent_id'];
        $orderData['onumber'] = $oNumber;
        $orderData['buytime'] = $createTime;
        $orderData['buyprice'] = sprintf("%.".$decimal."f", $buyPrice);;
        $orderData['pid'] = $orderPid;
        $orderData['ostyle'] = $orderType;
        $orderData['surplus'] = $surplus;
        $orderData['loss'] = $loss;
        $orderData['is_wei'] = 0;

        $orderData['eid'] = 2;
        //总费用
        $orderData['fee'] = sprintf("%.2f", $orderPrice);
        $orderData['ptitle'] = $productDetail['name'];
        $orderData['ostaus']='0';
        $orderData['sx_fee']=  sprintf("%.2f", $sxFee);
        $allFee = $orderData['fee'] * $moneyDefaultRate + $orderData['sx_fee'] * $moneyDefaultRate;
        $orderData['create_time'] = time();

        Db::startTrans();
        $res[] = $ids = $this->dao->model->insertGetId( $orderData);
//        dump($orderFeeData);die;

        $agentService = app(AgentService::class);
        //增加代理资金记录]
        foreach ($agentCommissions as $yong_agent_key => $agentCommission) {
            $y_money = round($agentCommission['money'], 2);
            if($agentCommission['agent_id'] != 0){
                $agentDetail = $agentService->dao->model->where('id', $agentCommission['agent_id'])->find();
                $adminBalance = $agentDetail['balance'] ?? 0;
                $adminNewBalance = $adminBalance + $y_money;
                $res[] = $agentService->dao->model->where('id', $agentCommission['agent_id'])->update([
                    'balance' => Db::raw('balance+'.$agentCommission['money'])
                ]);
                $res[] = app(WaterService::class)->dao->model->insert([
                    'member_id' => $member['id'] ?? 0,
                    'agent_id' => $agentCommission['agent_id'],
                    'money' => $y_money,
                    'remark' => '',
                    'source' => 'fee',
                    'source_id' => $ids,
                    'pay_type' => 'balance',
                    'member_balance' => $adminBalance,
                    'balance' => $adminNewBalance,
                    'describe' => '手续费'."累计",
                    'type'=>1,
                    'create_time' => $createTime,
                    'update_time' => $createTime,
                ]);
            }
        }

        //手续费返现
        if(!empty($member['fee_cash']) && $member['fee_cash'] > 0 && $sxFee > 0){
            $itemFee =  ($sxFee *  $member['fee_cash'] / 100);
            $res[] = app(MemberFeeCashWaterService::class)->dao->model->insert([
                'member_id' => $member['id'],
                'agent_id' => $member['agent_id'],
                'fee' => $sxFee,
                'money' =>  sprintf("%.4f",$itemFee),
                'settlement_status' => 0,
                'create_time'=>$createTime,
                'update_time'=>$createTime,
                "order_time"=>$createTime,
                'source_id'=>$ids,
                'source'=>'order',
                'describe'=>'手续费返现',
                "type"=>"commission"
            ]);

        }

        //分佣
        if(!empty($member['people_id'])){
            $peopleId = $member['people_id'];
            $peopleMember = app(MemberService::class)->dao->model->where('id', $peopleId)->find();
            //        邀请好友会员等级
            //        未邀请  LV1 返佣0%
            //        邀请1个 LV3 返佣20%
            //        邀请5人 LV6 返佣30%
            //        邀请10人LV9 返佣40%
            //        邀请30人 LV10 返佣60%
            //            people_tui   10%  20%   60%
            $peopleFee = 0;
            if($peopleMember['people_tui'] >= 21){
                $peopleFee = 60;
            }elseif ($peopleMember['people_tui'] >= 6){
                $peopleFee = 20;
            }elseif ($peopleMember['people_tui'] >= 1){
                $peopleFee = 10;
            }
            $peopleFee = 10;
            $peopleFeePrice = round($sxFee * $peopleFee / 100,4);
            if($peopleFeePrice > 0){
                $res[] = app(MemberCommissionWaterService::class)->dao->model->insert([
                    'people_id' => $peopleId,
                    'member_id' => $member['id'],
                    'agent_id' => $peopleMember['agent_id'] ?? 0,
                    'money' => $peopleFeePrice,
                    'status' => 0,
                    'create_time'=>$createTime,
                    'order_price'=>$orderPrice,
                    'order_id'=>$ids
                ]);
            }
//            dump($peopleFeePrice);die;
        }
        //点差返现  废弃
//        if ( false  &&　!empty($member['spread_cash']) && $member['spread_cash'] > 0
//            && !empty($productDetail['spread']) && $productDetail['spread']
//            && !empty($productDetail['leverage']) && $productDetail['leverage'] > 0
//        ){
//            //返佣=产品点差*杠杠*手数*返现比例 / 100
//            $productSpread = ($productDetail['spread'] * $productDetail['leverage'] * $oNumber);
//            $feeCommission = $productSpread * ($member['spread_cash'] / 100);
//            $res[] = app(MemberFeeCashWaterService::class)->dao->model->insert([
//                'member_id' => $member['id'],
//                'agent_id' => $member['agent_id'],
//                'fee' => $productSpread,
//                'money' =>  sprintf("%.4f", $feeCommission),
//                'settlement_status' => 0,
//                'create_time'=>$createTime,
//                'update_time'=>$createTime,
//                "order_time"=>$createTime,
//                'source_id'=>$ids,
//                'source'=>'order',
//                'describe'=>'点差返现',
//                "type"=>"spread"
//            ]);
//        }

        $res[] = app(OrderLogService::class)->dao->model->insert([
            'agent_id'=>$member['agent_id'] ?? 0,
            'member_id' => $member['id'],
            'username' => $member['username'],
            'pid' => $orderData['pid'],
            'ptitle' => $orderData['ptitle'],
            'ostyle' => $orderData['ostyle'],
            'onumber' => $orderData['onumber'],
            'price' => $orderData['buyprice'],
            'type' => 0,
            'oid' => $ids
        ]);
        if (!empty($res) && ArrayHelper::checkArr($res)) {
            Db::commit();
            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'createOrder','member_id'=>$memberId]);
            app(PersonService::class)->sendSubscribeMessage($memberId,$orderData['ptitle']);
            $orderData['id'] = $ids;
            return $orderData;
        } else {
            Db::rollback();
            $this->error = "下单失败，请重试!";
            return false;
        }
    }

    /*
     * 创建委托单
     */
    public function createWeiTuo($memberId,$orderType,$orderPrice,$createPrice,$oNumber,$surplus,$loss,$orderPid){

        if (empty($createPrice)) {
            $this->error = "标记价格不能为空!";
            return false;
        }
        //产品详细
        $productDetail = app(ProductCacheService::class)->getProductDetailCache($orderPid);
        if(empty($productDetail)){
            $this->error = '产品不存在!';
            return false;
        }

        //验证是否开市
        if(!empty($productCache['open_time']) && !ProductHelper::checkIsOpen($productDetail["open_time"])){
            $this->error = '休市不可开仓!';
            return false;
        }

        //验证状态
        if($productDetail['status']['value'] != 1){
            $this->error = '产品已关闭!';
            return false;
        }
        //保证金
        $productPayChoose = $productDetail['pay_choose'];
        if($productPayChoose == 0 ) {
            $this->error = '该产品未设置保证金!暂时不能开仓！';
            return false;
        }
        //保证金重新计算赋值
        $orderPrice = $productPayChoose * $oNumber;
        //验证余额是否够
        $memberService = app(MemberService::class);
        $member = $memberService->dao->model->with(['coin'])->where('id', $memberId)->find();
        //验证用户是否被冻结
        if(empty($member)){
            $this->error = "您的账户不存在！";
            return false;
        }
        if($member['status']['value'] != 1){
            $this->error = "您的账户已被冻结";
            return false;
        }

        if (!empty($productDetail['buy_min']) && $productDetail['buy_min'] != 0 && $oNumber < $productDetail['buy_min'] ){
            $this->error = "开仓最低".$productDetail['buy_min']."手!请重新开仓";
            return false;
        }

        if (!empty($productDetail['buy_max']) && $productDetail['buy_min'] != 0 && $oNumber > $productDetail['buy_max'] ){
            $this->error = "开仓最大".$productDetail['buy_max']."手!请重新开仓";
            return false;
        }
        $moneyDefaultRate = ProductHelper::moneyRate();
        //获取当前交易及钱包详细
        $memberTrade = app(MemberOrderService::class)->getMemberTradeInfo($memberId);

        $moneyDefaultRate = ProductHelper::moneyRate();
        if ($memberTrade['money_keyong'] < $orderPrice * $moneyDefaultRate){
            $this->error = "您得余额不足，请充值!";
            return false;
        }

        //开仓价格
        $buyPrice = $createPrice;

        //做空 卖出 买跌
        if ($orderType == 1){
            //委托单达标即成交 不计算滑点
            if($loss && $loss < $buyPrice){
                $this->error = "止损价不能低于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus > $buyPrice){
                $this->error = "止盈价不能高于{$buyPrice}";
                return false;
            }
            //做多 买入
        }else{
            //委托单达标即成交 不计算滑点
            if($loss && $loss > $buyPrice){
                $this->error = "止损价不能高于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus < $buyPrice){
                $this->error = "止盈价不能低于{$buyPrice}";
                return false;
            }
        }

        $productNowPrice = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->get(CacheKeyConstant::PRODUCT_PRICE.":".$orderPid);

        if(empty($productNowPrice)){
            $this->error = "当前行情异常!挂单失败 !";
            return false;
        }
        $orderData = [
            'agent_id'=>$member['agent_id'],
            'member_id'=>$memberId,
            'ptitle'=>$productDetail['name'],
            'ostyle'=>$orderType,
            'order_price'=>$orderPrice,
            'buyprice'=>$buyPrice,
            'onumber'=>$oNumber,
            'surplus'=>$surplus,
            'loss'=>$loss,
            'pid'=>$orderPid,
            'mark_price'=>$productNowPrice,
            'status'=>0,
            'create_time'=>time()
        ];

        Db::startTrans();
        $res[] = $ids = app(OrderRobotService::class)->dao->model->insertGetId( $orderData);

        if (!empty($res) && ArrayHelper::checkArr($res)) {
            Db::commit();
            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'createOrder','member_id'=>$memberId]);
            $orderData['id'] = $ids;
            return $orderData;
        } else {
            Db::rollback();
            $this->error = "挂单失败，请重试!";
            return false;
        }
    }

    /*
     * 委托单修改
     */
    public function updateWeiTuo($memberId, $buyPrice,$surplus,$loss,$orderId){
        $orderDetail = app(OrderRobotService::class)->dao->model->where('id',$orderId)->find();
        if (empty($orderDetail)){
            $this->error = "订单不存在!";
            return false;
        }

        $productDetail = app(ProductCacheService::class)->getProductDetailCache($orderDetail['pid']);

        $buyPrice = floor($buyPrice * pow(10,$productDetail['decimal'])) / pow(10,$productDetail['decimal']);
        if (!empty($surplus)){
            $surplus = floor($surplus * pow(10,$productDetail['decimal'])) / pow(10,$productDetail['decimal']);
        }else{
            $surplus = '';
        }
        if (!empty($loss)){
            $loss = floor($loss * pow(10,$productDetail['decimal'])) / pow(10,$productDetail['decimal']);
        }else{
            $loss = '';
        }

        //做空 卖出 买跌
        if ($orderDetail['ostyle']['value'] == 1){
            //委托单达标即成交 不计算滑点
            if($loss && $loss < $buyPrice){
                $this->error = "止损价不能低于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus > $buyPrice){
                $this->error = "止盈价不能高于{$buyPrice}";
                return false;
            }
            //做多 买入
        }else{
            //委托单达标即成交 不计算滑点
            if($loss && $loss > $buyPrice){
                $this->error = "止损价不能高于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus < $buyPrice){
                $this->error = "止盈价不能低于{$buyPrice}";
                return false;
            }
        }

        $orderData = [
            'buyprice'=>$buyPrice,
            'surplus'=>$surplus,
            'loss'=>$loss,
            'update_time'=>time()
        ];
        Db::startTrans();
        $res[] = app(OrderRobotService::class)->dao->model->where('id',$orderId)->update($orderData);
        if (!empty($res) && ArrayHelper::checkArr($res)) {
            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'updateWeiTuo','member_id'=>$memberId]);
            Db::commit();
            return true;
        }
        Db::rollback();
        $this->error = "修改失败，请重试!";
        return false;
    }

    public function pingCang($memberId, $orderId,$sellType = '',$other = []){
        $nowTime = time();
        $orderDetail = $this->dao->model->where('id',$orderId)->find();
        if (empty($orderDetail)){
            $this->error = "订单不存在!";
            return false;
        }
        if($orderDetail['ostaus']['value'] !== 0){
            $this->error = "订单已平仓!";
            $this->errorCode = 2;
            return false;
        }
        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':sell:'.$memberId.':'.$orderId)){
            $this->error = "请勿重复操作!";
            $this->errorCode = 2;
            return false;
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':sell:'.$memberId.':'.$orderId,1,2);

        $memberService = app(MemberService::class);
        $memberDetail = $memberService->getCacheDetail($memberId);
        if (empty($memberDetail)){
            $this->error = "会员不存在!";
            return false;
        }
        $createTime = StringHelper::_strtotime($orderDetail['create_time']);
        if(!empty($memberDetail['order_pin_time']) && $memberDetail['order_pin_time'] > 0){
            if((time() - $createTime) <= $memberDetail['order_pin_time']){
                $this->error = "订单".$memberDetail['order_pin_time']."秒内不可平仓";
                return false;
            }
        }else if(!empty(sysconf('order_ping_time')) && sysconf('order_ping_time') > 0){
            if ((time() - $createTime) <= sysconf('order_ping_time') ) {
                $this->error = "新订单".sysconf('order_ping_time')."秒内不可平仓";
                return false;
            }
        }

        $productDetail = app(ProductCacheService::class)->getProductDetailCache($orderDetail['pid']);

        //验证是否开市
        if(!empty($productCache['open_time']) && !ProductHelper::checkIsOpen($productDetail["open_time"])){
            $this->error = '休市不可开仓!';
            return false;
        }

        $marketCacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);

        $productNowPrice = $marketCacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$orderDetail['pid']);

        if (empty($productNowPrice)){
//            $this->error = "行情价格获取失败!请稍后重试!";
            $this->error = "行情休市!请稍后重试!";
            return false;
        }

        //滑点
        if($sellType === SellTypeEnum::USER && !$memberDetail['moni']){
            $memberSlippage = $memberDetail['slippage'] ?? null;
            $slippage = $memberService->getMemberSlippage($orderDetail['pid'],$memberSlippage,false);
            if(!empty($slippage)){
                $productNowPrice = ProductHelper::setMemberProHuaDian( $orderDetail['ostyle']['value'],$productNowPrice,$slippage,2);
            }else{
                $productNowPrice = ProductHelper::setProHuaDian($orderDetail['ostyle']['value'],$productNowPrice,$productDetail['sell_slippage'],2);
            }
        }else if (in_array($sellType,[
                SellTypeEnum::MARK,
                SellTypeEnum::LOSS,
                SellTypeEnum::SURPLUS,
            ]) && !empty($other["now_sell_price"])){
            $productNowPrice = $other["now_sell_price"];
        }

        $productRiskCacheKey  ="memberOrderRisk:".$memberId.":".$orderDetail["pid"];
        $riskCacheData = [];
        $productRiskCacheTime = 0;
        $riskCacheStatus = false;
        //风控滑点
        if(!$memberDetail['moni'] && !empty($memberDetail['risk']) ){
            $memberRisk = json_decode($memberDetail['risk'],true);
            if (isset($memberRisk[$orderDetail['pid']])  && !empty($memberRisk[$orderDetail['pid']]) ){
                $productRisk = $memberRisk[$orderDetail['pid']];
                if ($productRisk["status"] == 1  && $productRisk["trigger_time"] > 0){
                    $productPrice = $productRisk["price"];
                    $productRiskCache = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->get($productRiskCacheKey);
                    if((time() - $createTime) <= $productRisk['trigger_time'] && $productRisk["price"]){
                        //缓存风控滑点
                        if (empty($productRiskCache) ||
                            (time() - $productRiskCache["create_time"] > $productRisk["monitor_time"] * 60 * 60)
                            ){
                            $productRiskCacheTime = (int)($productRisk["monitor_time"] * 60 * 60);
                            $riskCacheData = [
                                "count" => 1,
                                "create_time" => time(),
                                "price"=>$productPrice,
                                "cache_time"=>$productRiskCacheTime
                            ];
                        }else{
                            $riskCacheData = $productRiskCache;
                            $riskCacheData["count"]++;
                            $productPrice = $riskCacheData["count"] * $productPrice;
                            $productRiskCacheTime = (int)($productRisk["monitor_time"] * 60 * 60 - (time() - $riskCacheData["create_time"]));
                            $riskCacheData["cache_time"] = $productRiskCacheTime;
                        }
                        $riskCacheData["risk_price"] = $productPrice;
                        //执行风控滑点
                        $productNowPrice = ProductHelper::setMemberProHuaDian( $orderDetail['ostyle']['value'],$productNowPrice,$productPrice,2);
                        $other['other_data']["risk"] = $riskCacheData;
                        $riskCacheStatus = true;
                    }
                    //dump($productRisk,$productNowPrice,$productPrice,$riskCacheData);die;
                }
            }
        }




        //默认买涨
        $yingkui = 0;
        //平仓盈亏
        if($productDetail['number'] != 0){
            $yingkui = ProductHelper::orderProfit($productNowPrice, $productDetail['number'], $productDetail['money'], $orderDetail['buyprice'], $orderDetail['onumber']);
        }

        //如果买跌
        $orderDetail['ostyle']['value'] == 1 && $yingkui = $yingkui * -1;

        $moneyDefaultRate = ProductHelper::moneyRate();
        $result = [];

        $memberCoin = app(MemberCoinService::class)->dao->model->where('member_id', $orderDetail['member_id'])->find();
        //平仓增加用户金额
        $u_add = ($yingkui - $orderDetail['sx_fee']) * $moneyDefaultRate;


        Db::startTrans();
        try {

            $result[] = app(MemberCoinService::class)->dao->model->where('member_id', $orderDetail['member_id'])->update([
                'balance' => Db::raw('balance+' . $u_add),
                'yingkui_total'=> Db::raw('yingkui_total+' . $u_add),
            ]);

            if ($u_add > 0) {
                $plusminus = 1;
                $u_add_true = $u_add;
            } else {
                $plusminus = 0;
                $u_add_true = $u_add * -1;
            }
            if ($yingkui > 0) {  //盈利
                $d_map['is_win'] = 1;
            }elseif ($yingkui <= 0) {    //亏损
                $d_map['is_win'] = 2;

            }
            //写入日志
            $result[] = app(WaterService::class)->dao->model->insert([
                'member_id' => $memberDetail['id'],
                'agent_id' => $memberDetail['agent_id'],
                'money' => $u_add_true * $moneyDefaultRate,
                'remark' => '',
                'source' => 'settlement',
                'source_id' => $orderDetail['id'],
                'pay_type' => 'balance',
                'member_balance' => $memberCoin['balance'] ,
                'balance' =>$u_add > 0? ($memberCoin[ 'balance'] + $u_add_true * $moneyDefaultRate) :  ($memberCoin['balance'] - $u_add_true * $moneyDefaultRate),
                'describe' => '结算盈利',
                'type'=>$u_add > 0 ?1:0,
                'create_time' => time(),
            ]);
            $result[] = app(OrderLogService::class)->dao->model->insert([
                'member_id' => $memberDetail['id'],
                'agent_id' => $memberDetail['agent_id'],
                'username' => $memberDetail['username'],
                'pid' => $orderDetail['pid'],
                'ptitle' => $orderDetail['ptitle'],
                'ostyle' => $orderDetail['ostyle']['value'],
                'onumber' => $orderDetail['onumber'],
                'price' => $productNowPrice,
                'type' => 1,
                'oid' => $orderDetail['id'],
            ]);

            //结算佣金
            $result[] = app(MemberCommissionWaterService::class)->settlement($orderDetail['id']);

            //平仓处理订单
            $d_map['ostaus'] = 1;
            $d_map['sellprice'] = $productNowPrice;
            $d_map['selltime'] = $nowTime;
            $d_map['sell_type'] = $sellType;
            $d_map['ploss'] = $yingkui;
            $d_map['id'] = $orderDetail['id'];

            $result[] = app(OrderService::class)->dao->model->where('id', $orderDetail['id'])->update($d_map);
            //平仓写入redis
            if (ArrayHelper::checkArr($result)) {
//            $memberTradeInfo = $this->getMemberTradeInfo($orderDetail['member_id']);
                $log['order_id'] = $orderDetail['id'];
                $log['member_id'] = $orderDetail['member_id'];
                $log['agent_id'] = $orderDetail['agent_id'];
                $log['pid'] = $orderDetail['pid'];
                $log['sellprice'] = $productNowPrice;
                $log['selltime'] = $nowTime ;
                $log['sell_type'] = $sellType;
                $log['other_data'] = $other['other_data'] ?? '';
                $log['user_jiao_info'] = $other['user_jiao_info'] ?? $this->getMemberTradeInfo($orderDetail['member_id']);
                $log['order_data'] = $orderDetail;
                Queue::push("app\common\jobs\CreateOrderPingCangLogJob", $log, 'OrderPingCangLogJob');
                Db::commit();


                $riskCacheStatus && app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->set($productRiskCacheKey,$riskCacheData,(int)($productRiskCacheTime + 2));

                app(MemberService::class)->deleteCacheDetail($orderDetail['member_id']);
//            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'pingCang','member_id'=>$memberId]);
                return [
                    'sell_price' => $productNowPrice,
                    'yingkui' => $u_add,
                    'order_id' => $orderId,
                    'sell_time' => date('Y-m-d H:i:s',$nowTime),
                    'onumber'=>$orderDetail['onumber'],
                    'product_name'=>$productDetail['name'],
                    'ostyle' => $orderDetail['ostyle']['value'],
                ];
            }
            Db::rollback();
            $this->error = "平仓失败，请重试!";
            return false;
        }catch (\Exception $e){
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    /*
     * 平仓亏损最多的订单
     */
    public function pingCangMax($orderHold = [],$memberId = 0,$sellType = '',$other = []){
        //平仓亏损最多的单，平不掉就平下一单

        $orderChi = ArrayHelper::arraySort($orderHold,'yingkui_t',SORT_ASC);
        $pingStatus = false;
        foreach ($orderChi as $k => $v) {
            //平仓
            $v['sys_ping'] = 1;
            return $this->pingCang($memberId, $v['id'],$sellType, $other);
            break;
        }
    }
    public function pingCangZhiDing($memberId = 0,$orderId = 0,$sellType = '',$other = []){
        {
            return $this->pingCang($memberId,$orderId,$sellType, $other);
        }
    }
    /*
     * [addGuarantee APP修改止损]
     */
    public function upYsOrder(
        $memberId,
        $triggerPrice,
        $markPrice,
        $surplus,
        $loss,
        $oid
    ){

        $orderDetail = $this->dao->model->where('id',$oid)->find();
        if (empty($orderDetail)) {
            $this->error = '持仓不存在';
            return false;
        }
        $createTime = strtotime($orderDetail['buytime']);
        $memberDetail = app(MemberService::class)->getDetail($memberId);
        if(!empty($memberDetail['order_pin_time']) && $memberDetail['order_pin_time'] > 0){
            if((time() - $createTime) <= $memberDetail['order_pin_time']){
                $this->error = "订单".$memberDetail['order_pin_time']."秒内不可设置止盈止损";
                return false;
            }
        }else if(!empty(sysconf('order_ping_time')) && sysconf('order_ping_time') > 0){
            if ((time() - $createTime) <= sysconf('order_ping_time') ) {
                $this->error = "新订单".sysconf('order_ping_time')."秒内不可设置止盈止损";
                return false;
            }
        }

        $productDetail = app(ProductCacheService::class)->getProductDetailCache($orderDetail['pid']);

        $decimal = $productDetail['decimal'];
        //处理止盈止损价格位数
        $surplus = empty($surplus) ? "" : round((float)$surplus,$decimal);
        $loss = empty($loss) ? "" : round((float)$loss,$decimal);

        $productNowPrice = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->get(CacheKeyConstant::PRODUCT_PRICE.":".$orderDetail['pid']);

        if (empty($productNowPrice)){
            $this->error = "行情价格获取失败!请稍后重试!";
            return false;
        }
        //开仓价格
        $buyPrice = $orderDetail['buyprice'] ?? $productNowPrice;

        //做空 卖出 买跌
        if ($orderDetail['ostyle']['value'] == 1){
            //现价单
            if($loss && $loss < $buyPrice){
                $this->error = "止损价不能低于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus > $buyPrice){
                $this->error = "止盈价不能高于{$buyPrice}";
                return false;
            }
            //做多 买入
        }else{
            //现价单
            if($loss && $loss > $buyPrice){
                $this->error = "止损价不能高于{$buyPrice}";
                return false;
            }
            if($surplus && $surplus < $buyPrice){
                $this->error = "止盈价不能低于{$buyPrice}";
                return false;
            }
        }


        if (!empty($markPrice)){
            $markPrice = floor($markPrice* pow(10,$decimal)) / pow(10,$decimal);
            $triggerPrice = floor($productNowPrice * pow(10,$decimal)) / pow(10,$decimal);

        }else{
            $markPrice = '';
            $triggerPrice = $triggerPrice ?? "";
        }

        Db::startTrans();
          $res[] = $this->dao->model->where('id',$oid)->update([
                'surplus' => $surplus,
                'loss' => $loss,
                'mark_price' => $markPrice,
                'trigger_price' => $triggerPrice,
                'is_wei' => 1,
                'update_time' => time(),
              ]);
          if(!empty($res) && ArrayHelper::checkArr($res)){
              app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'upYsOrder','member_id'=>$memberId]);
              Db::commit();
              return true;
          }
          Db::rollback();
        return false;
    }
}