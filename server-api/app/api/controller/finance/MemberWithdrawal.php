<?php
/**
 * @Date: 2025/6/29 17:28
 */
declare(strict_types=1);
namespace app\api\controller\finance;
use app\common\constants\CacheKeyConstant;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\services\common\CacheService;
use app\common\services\order\MemberOrderService;
use app\common\services\ProductCacheService;
use think\facade\App;
use app\common\services\finance\MemberWithdrawalService;

/**
 * Class MemberWithdrawal
 */
class MemberWithdrawal extends \app\api\controller\Base
{
    /**
     * MemberWithdrawal constructor.
     * @param App $app
     * @param MemberWithdrawalService $service
     */
    public function __construct(App $app, MemberWithdrawalService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 添加
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['money', ''],
            ['fee', ''],
            ['remark', ''],
            ['pay_type',''],
            ['type','balance']
        ]);

        $targetDate = strtotime('2026-02-25');
        $now = time();
        if ($targetDate >= $now) {
           $this->error('2月10至2月24号为春节假期，春节假期将暂停出金，2月25号恢复正常出金！');
        }
        if($data['type'] === WithdrawalTypeEnum::BALANCE && !$this->service->checkWorkTime()){
            $this->error('不好意思，提现时间周一到周五 早上9点到17点！');
        }
//        elseif ($data['type'] === WithdrawalTypeEnum::COMMISSION&& !$this->service->checkCommissionTime()){
//            $this->error('不好意思，今天您不可提现。请在每月的5日、15日或25日提交！');
//        }

        //持仓单
        $MemberTrade = app(MemberOrderService::class)->getMemberTradeInfo($this->uid);
        if (isset($MemberTrade['count']) && $MemberTrade['count'] > 0) {
            $this->error('当前账户有持仓，请先平仓！');
        }
//        $hold_chi = app(MemberOrderService::class)->getChiCangOrdersByMemberId($this->uid);
//        if(count($hold_chi) > 0 && $data['type'] === WithdrawalTypeEnum::BALANCE){
//            $this->error('当前账户有持仓，请先平仓！');
//        }

        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':withdrawal:'.$this->uid)){
            $this->error("请勿重复操作");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':withdrawal:'.$this->uid,1,10);

        sleep(2);
        $myzcMin = 0.0001;
        $myzcMax = 10000000;
        $balance = $this->member['balance'];
        if($data['type'] == WithdrawalTypeEnum::COMMISSION ){
            $myzcMin = 1;
            $balance = $this->member['commission_balance'];
        }
        if ($data['money'] < $myzcMin) {
            $this->error('数量不能低于'.$myzcMin.'！');
        }
        if ($myzcMax < $data['money']) {
            $this->error('数量不能大于'.$myzcMax.'！');
        }

        if($data['type'] == WithdrawalTypeEnum::BALANCE && $data['money'] > $balance - $MemberTrade['baozhengjin_sum']){
            $this->error('可用余额不足！');
        }elseif ($data['money'] > $balance) {
            $this->error('可用余额不足！');
        }
        if($data['type'] == WithdrawalTypeEnum::BALANCE && $data['pay_type'] == 'offline_bank' && empty($this->member['bank_code'])){
            $this->error('请先绑定银行卡！');
        }
        if($data['type'] == WithdrawalTypeEnum::BALANCE && $data['pay_type'] == 'offline_bank' && $data['money'] < 350){
            $this->error('银行卡最低提350u以上，小额请提支付宝！');
        }
        if($data['type'] == WithdrawalTypeEnum::BALANCE && $data['pay_type'] == 'offline_usdt' && empty($this->member['usdt_card'])){
            $this->error('请先绑定usdt地址！');
        }

        $memberWithdrawalFee = sysconf('member_withdrawal_rate');
        if($data['type'] == WithdrawalTypeEnum::BALANCE && $data['pay_type'] == 'offline_usdt'){
            $memberWithdrawalFee = sysconf('member_usdt_withdrawal_rate');
        }

        $num = $data['money'];
        if ($memberWithdrawalFee) {
            $fee = round(($num / 100) * $memberWithdrawalFee, 2);
            $mum = round($num - $fee, 2);

            if ($mum < 0) {
                $this->error('转出手续费错误！');
            }
            if ($fee < 0) {
                $this->error('转出手续费设置错误！');
            }
        } else {
            $fee = 0;
            $mum = $num;
        }
        if($data['type'] === WithdrawalTypeEnum::COMMISSION){
            $fee = 0;
            $mum = $data['money'];
        }
        $data['member_id'] = $this->uid;
        $data['agent_id'] = $this->member['agent_id'];
        $data['amount'] = $mum;
        $data['fee'] = $fee;
        if( $this->service->createSave($data)){
            $this->success('申请成功!');
        }
        $this->error($this->service->getError()?:'申请失败!');
    }
}