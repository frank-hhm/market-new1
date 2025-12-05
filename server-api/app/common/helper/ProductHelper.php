<?php
/**
 * @Date: 2025/1/10 15:33
 */
declare(strict_types=1);
namespace app\common\helper;
use app\common\services\agent\AgentService;
use app\common\services\common\CacheService;
use app\common\services\ProductService;

/**
 * 产品帮助类
 * Class ProductHelper
 * @package app\common\helper
 */
class ProductHelper
{

    //返回当前的毫秒时间戳
    public static function msecTime(): float
    {
        $microTimeArr = explode(' ', microtime());
        $msec = $microTimeArr[1];
        $sec = $microTimeArr[0];
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    /**
     * 验证是否休市
     */
    public static function checkIsOpen($openTime,$minute=0): bool
    {
        if(empty($openTime)){
            return false;
        }
        $isOpen = false;
        //此时时间
        $_zhou = (int)date("w");
        $_Hi = date("H:i");
        $openTimeArr = $openTime ?? null;
        if (!$openTimeArr) {
            $openTimeArr = ['', '', '', '', '', '', ''];
        }elseif(!is_array($openTimeArr)){
            $openTimeArr = json_decode($openTimeArr,true);
        }
//        dump($openTimeArr);die;
        foreach ($openTimeArr as $k => $v) {
            if ($k == $_zhou && !empty($v)) {
                if(is_array($v)){
                    continue;
                }
                $_check = explode('|',$v);
                if(!$_check){
                    continue;
                }
                foreach ($_check as $key => $value) {
                    $_check_shi = explode('~',$value);
                    if(count($_check_shi) != 2 && !isset($_check_shi[0])&& !isset($_check_shi[1])){
                        continue;
                    }
                    $_check_shi[0] = date('H:i', strtotime($_check_shi[0])+($minute*60));

                    //开市时间在1与2之间
                    if($isOpen){
                        continue;
                    }
                    //23:59
                    if($_check_shi[1] == "23:59"){
                        $_check_shi[1]  = "24:00";
                    }
                    if($_check_shi[0] <= $_Hi && $_Hi <= $_check_shi[1]
                    ){
                        $isOpen = true;
                    }else{
                        $isOpen = false;
                    }
                }
            }
        }
//        dump($isOpen);die;
        return $isOpen;
    }

    /*
     * 检测是否符合隔夜强平时间
     */
    public static function checkIsNightClose($yesterdayCloseTime = []): bool
    {
        //按周获取数组里今天的配置
        $status = false;
//        dump($yesterdayCloseTime[$today]);die;
        if (!empty($yesterdayCloseTime)) {
            $yesterdayCloseArr =  explode('|', $yesterdayCloseTime);
            $nowDataHi = date('H:i');
            foreach ($yesterdayCloseArr as $k_sa => $v_sa) {
                if (!$v_sa) {
                    continue;
                }
                if ($nowDataHi == $v_sa) {
                    $status = true;
                    break;
                }
            }
        }
        return $status;
    }


    /**
     * 获取绝对值
     */
    public static function getAbs($keys)
    {
        if($keys > 0) return  $keys;
        else return  -$keys;
    }
    /**
     * 获取比例
     */
    public static function moneyRate(){
        $money_rate = 1;
        return $money_rate;
    }

    /**
     * 获取手续费
     */
    public static function getProductCreateOrderFee($member,$productDetail, $oNumber = 1): array
    {
        $productId = $productDetail['id'];
        $agentCommissions = [];
        $ratioFeeSum = 0;
        try {
            $agentService = app(AgentService::class);
            //代理
            $agentDetail = $agentService->getAgentCache($member['agent_id']);
            //代理等级
            $agentLevelArr = [1, 2, 3];
            $agentLevel = $agentDetail['level']['value'] ?? 4;
            //产品设置的手续费
            $productFeeBuy = $productDetail['fee_buy'] ?? 0;
            //返佣代理-代理有效才返代理
            if (!empty($agentDetail) && in_array($agentLevel,$agentLevelArr)) {
                //平台手续费
                $systemFee = $productFeeBuy;
                $agent1Fee = 0;
                $agent2Fee = 0;
                $agent3Fee = 0;
                $agentFee = [];
                switch ($agentLevel) {
                    case 1:
                        //一级代理
                        $agentDetail1 = $agentDetail;
                        $agentDetail1Fee = $agentService->getInitFee($agentDetail1['ratio_agent_fee'] ?? '' );

                        if (!empty($agentDetail1Fee[$productId]) ){
                            if(!empty($agentDetail1Fee[$productId]['dist'])){
                                $systemFee = $agentDetail1Fee[$productId]['dist'] ;
                            }
                            if(!empty($agentDetail1Fee[$productId]['markup'])){
                                $agent1Fee = $agentDetail1Fee[$productId]['markup'] ;
                            }
                        }
                        break;
                    case 2:
                        //二级代理
                        $agentDetail2 = $agentDetail;
                        $agentDetail2Fee = $agentService->getInitFee($agentDetail2['ratio_agent_fee']);

                        //获取二级代理上级代理一级
                        if (!empty($agentDetail2['pid'])) {
                            $agentDetail1 = $agentService->getAgentCache($agentDetail2['pid']);
                            $agentDetail1Fee = $agentService->getInitFee($agentDetail1['ratio_agent_fee'] ?? '' );
                        }

                        if (!empty($agentDetail1Fee[$productId]) ){
                            if(!empty($agentDetail1Fee[$productId]['dist'])){
                                $systemFee = $agentDetail1Fee[$productId]['dist'] ;
                            }
                            if(!empty( $agentDetail1Fee[$productId]['markup'])){
                                $agent1Fee = $agentDetail1Fee[$productId]['markup'] ;
                            }
                        }

                        //二级代理的手续费
                        if (!empty($agentDetail2Fee[$productId])){
                            if(!empty($agentDetail2Fee[$productId]['dist']) && !empty($agentDetail2Fee[$productId]['markup'])){
                                $agent2Fee = $agentDetail2Fee[$productId]['dist'] + $agentDetail2Fee[$productId]['markup'];
                            }
                        }

                        break;
                    case 3:
                        //三级代理
                        $agentDetail3 = $agentDetail;
                        $agentDetail3Fee = $agentService->getInitFee($agentDetail3['ratio_agent_fee']);

                        //获取三级代理上级代理二级
                        if (!empty($agentDetail3['pid'])) {
                            //获取二级代理
                            $agentDetail2 = $agentService->getAgentCache($agentDetail3['pid']);
                            $agentDetail2Fee = $agentService->getInitFee($agentDetail2['ratio_agent_fee'] ?? '' );

                            //获取二级代理上级代理一级
                            if (!empty($agentDetail2['pid'])) {
                                $agentDetail1 = $agentService->getAgentCache($agentDetail2['pid']);
                                $agentDetail1Fee = $agentService->getInitFee($agentDetail1['ratio_agent_fee'] ?? '' );
                                $agentId1 = $agentDetail1['id'];
                            }
                        }

                        if (!empty($agentDetail1Fee[$productId]) ){
                            if(!empty($agentDetail1Fee[$productId]['dist'])){
                                $systemFee = $agentDetail1Fee[$productId]['dist'] ;
                            }
                            if(!empty($agentDetail1Fee[$productId]['markup'])){
                                $agent1Fee = $agentDetail1Fee[$productId]['markup'] ;
                            }
                        }
                        if (!empty($agentDetail2Fee[$productId])){
                            if(!empty($agentDetail2Fee[$productId]['dist']) && !empty($agentDetail2Fee[$productId]['markup'])){
                                $agent2Fee = $agentDetail2Fee[$productId]['dist'] + $agentDetail2Fee[$productId]['markup'];
                            }
                        }
                        if (!empty($agentDetail3Fee[$productId])){
                            if(!empty($agentDetail3Fee[$productId]['dist']) && !empty($agentDetail3Fee[$productId]['markup'])){
                                $agent3Fee = $agentDetail3Fee[$productId]['dist'] + $agentDetail3Fee[$productId]['markup'];
                            }
                        }
                        break;
                    default:
                        break;
                }

                if ($systemFee && $systemFee > 0) {
                    $moneyInc = $systemFee * $oNumber;
                    $agentCommissions[0] = [
                        'agent_id' => 0,
                        'money' => $moneyInc,
                        'remarks' => '平台手续费',
                    ];
                    $ratioFeeSum += $systemFee;
                }
                if (!empty($agentDetail1['id']) && $agent1Fee && $agent1Fee > 0) {
                    $moneyInc = $agent1Fee * $oNumber;
                    $agentCommissions[$agentDetail1['id']] = [
                        'agent_id' => $agentDetail1['id'],
                        'money' => $moneyInc,
                        'remarks' => '一级代理手续费',
                    ];
                    $ratioFeeSum += $agent1Fee;
                }
                if (!empty($agentDetail2['id']) && $agent2Fee && $agent2Fee > 0) {
                    $moneyInc = $agent2Fee * $oNumber;
                    $agentCommissions[$agentDetail2['id']] = [
                        'agent_id' => $agentDetail2['id'] ,
                        'money' => $moneyInc,
                        'remarks' => '二级代理手续费',
                    ];
                    $ratioFeeSum += $agent2Fee;
                }
                if (!empty($agentDetail3['id']) &&$agent3Fee && $agent3Fee > 0) {
                    $moneyInc = $agent3Fee * $oNumber;
                    $agentCommissions[$agentDetail3['id']] = [
                        'agent_id' => $agentDetail3['id'] ,
                        'money' => $moneyInc,
                        'remarks' => '三级代理手续费',
                    ];
                    $ratioFeeSum += $agent3Fee;
                }
            }
            $res['ratio_fee_sum'] = $ratioFeeSum;
            $res['agent_commissions'] = $agentCommissions;
            return $res;
        } catch (\Exception $e) {
            return [
                'ratio_fee_sum' => 0,
                'agent_commissions' => []
            ];
        }
    }


    /*
     * 计算盈亏
     */
    public static function orderProfit($productNotPrice,$productNumber,$productMoney,$buyPrice,$orderOnumber): float
    {
        $priceCha = $productNotPrice - $buyPrice;
        $profit = $priceCha / $productNumber * $productMoney * $orderOnumber;
        return (float)sprintf("%.2f",$profit);
    }


    /**
     * 获取用户滑点
     */
    public static function setMemberProHuaDian($orderType,$price,$slippage = 0,$isBuy = 1){
        if($slippage === 0){
            return $price;
        }
        if($isBuy === 1){
            if($orderType == 1){
                $price -= $slippage;
            }elseif ($orderType == 2){
                $price += $slippage;
            }
        }else{
            if($orderType == 1){
                $price += $slippage;
            }elseif ($orderType == 2){
                $price -= $slippage;
            }
        }
        return $price;
    }

    /*
     * 产品有滑点
     */
    public static function setProHuaDian($orderType,$price = 0,$huaDian = 0,$isBuy = 1)
    {
        //买跌
        if(empty($huaDian)){
            return $price;
        }
        if($isBuy === 1) {
            if ($orderType == 1) {
                $price -= $huaDian;
            } elseif ($orderType == 0) {
                $price += $huaDian;
            }
        }else{
            if ($orderType == 1) {
                $price += $huaDian;
            } elseif ($orderType == 0) {
                $price -= $huaDian;
            }
        }
        return $price;
    }
}