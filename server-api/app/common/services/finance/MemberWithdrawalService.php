<?php
/**

 * @Date: 2024/3/7 11:38
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\MemberWithdrawalDao;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\WithdrawalStatusEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use app\common\services\order\MemberOrderService;
use DateTime;
use DateTimeZone;
use think\facade\Db;

/**
 * 用户提现
 * Class MemberWithdrawalService
 */
class MemberWithdrawalService extends BaseService
{
    /**
     * MemberWithdrawalService constructor.
     * @param MemberWithdrawalDao $dao
     */
    public function __construct(MemberWithdrawalDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = $this->getFilter($param,$agentIds);
        $list = $this->dao->model->with(['member','agent'])->when(!empty($param['username_like']), function ($query) use ($param){
            return $query->hasWhere('member',[['username|mobile|nickname', 'like', "%{$param['username_like']}%"]]);
        })->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();

        return $list;
    }

    public function getFilter(array $param = [],$agentIds = [])
    {
        $filter = [];
        $agentKey = 'agent_id';
        $statusKey = 'status';
        if(!empty($param['username_like'])){
            $agentKey = 'MemberWithdrawalModel'.'.'.$agentKey;
            $statusKey = 'MemberWithdrawalModel'.'.'.$statusKey;
        }
        if(isset($param['status']) && $param['status'] != ''){
            $filter[] = [$statusKey, '=', $param['status']];
        }
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
           $filter[] = [$agentKey,  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = [$agentKey,'in',$agentIds];
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        return $filter;
    }
    public function getSum(array $param = [],$agentIds = [],$key = 'money')
    {
        $filter = $this->getFilter($param,$agentIds);
        $payTypeEnum = RechargePayTypeEnum::data();
        $statusEnum = WithdrawalStatusEnum::data();
        $list = [];
        foreach ($statusEnum as $k1 => $v1){
            $statusData = [
                'title'=>$v1['name'],
                'data' => []
            ];
            foreach ($payTypeEnum as $k => $item){
                if(!in_array($k,[
                    RechargePayTypeEnum::TURNTABLE,
                    RechargePayTypeEnum::BALANCE,
                    RechargePayTypeEnum::COMMISSION_BALANCE,
                ])) {
                    $count = $this->dao->model->with(['member', 'agent'])->when(!empty($param['username_like']), function ($query) use ($param) {
                        return $query->hasWhere('member', [['username|mobile|nickname', 'like', "%{$param['username_like']}%"]]);
                    })->where('pay_type', '=', $k)->where('status', '=', $k1)->where($filter)->sum('amount');;
                    $statusData['data'][] = [
                        'name' => $item['name'],
                        'value' => $count,
                    ];
                }
            }
            $list[] = $statusData;
        }
        return $list;
    }

    /**
     * 新增
     */
    public function createSave($data, $describeParam = '提现申请')
    {
        //持仓限制
//		$allfee = app(OrderService::class)->dao->model->where('ostaus',0)->where('member_id',$data['member_id'])->sum('fee');
//        $allfee = $allfee?$allfee:0;
//        if ($allfee){
//            $this->error = "您有持仓订单，请平仓后提现！";
//            return false;
//        }
        Db::startTrans();
        try {
            if($data['type'] === WithdrawalTypeEnum::BALANCE) {
                $res = $this->dao->model->create(array_merge([
                    'describe' => $describeParam,
                ], $data));
                $id = $res->id;
            }else{
                $res = true;
                $id = 0;
            }
            #更新余额
            $memberBalance = 0;
            $memberBalance1 = 0;
            $MemberTrade = app(MemberOrderService::class)->getMemberTradeInfo($data['member_id']);
            $baozhengjinSum = $MemberTrade['baozhengjin_sum'] ?? 0;
            if($res){
                $memberCoin = app(MemberCoinService::class)->dao->model->where('member_id',$data['member_id'])->find();
                $memberBalance = $memberCoin['balance'];
                $memberBalance1 = $memberBalance;

                if($data['type'] == WithdrawalTypeEnum::BALANCE){
                    if($memberCoin['balance'] - $baozhengjinSum - ($data['amount']+$data['fee']) < 0){
                        $message = "余额不足！";
                        $baozhengjinSum > 0 && $message = "余额不足!保证金占用:".$baozhengjinSum;
                        $this->error = $message;
                        return false;
                    }
                    $res = $memberCoin->save(['balance'=>Db::raw('balance-'.($data['amount']+$data['fee']))]);
                }else{
                    $memberBalance = $memberCoin['commission_balance'];
                    if($memberCoin['commission_balance']  - ($data['amount']+$data['fee']) < 0){
                        $this->error = "佣金余额不足！";
                        return false;
                    }
                    $res = $memberCoin->save([
                        'commission_balance'=>Db::raw('commission_balance-'.($data['amount']+$data['fee'])),
                        'balance'=>Db::raw('balance+'.$data['amount']),
                    ]);
                }

                if($res){
                    app(MemberService::class)->deleteCacheDetail($data['member_id']);
                    $res = app(WaterService::class)->dao->model->insert([
                        'member_id' => $data['member_id'],
                        'agent_id' => $data['agent_id'],
                        'money' => ($data['amount']+$data['fee']),
                        'remark' => '',
                        'source' => ( $data['type'] === WithdrawalTypeEnum::BALANCE?SourceEnum::MEMBER_WITHDRAWAL :SourceEnum::MEMBER_COMMISSION_WITHDRAWAL),
                        'source_id' => $id,
                        'pay_type' => ( $data['type'] === WithdrawalTypeEnum::BALANCE?WithdrawalTypeEnum::BALANCE : WithdrawalTypeEnum::COMMISSION),
                        'member_balance' => $memberBalance,
                        'balance' => $memberBalance -($data['amount']+$data['fee']),
                        'describe' => ( $data['type'] === 'balance'?'余额' : '佣金提现至余额'),
                        'type'=>0,
                        'create_time' => time(),
                        'update_time' => time(),
                    ]);

                    if($res && $data['type'] === WithdrawalTypeEnum::COMMISSION){
                        $res = app(WaterService::class)->dao->model->insert([
                            'member_id' => $data['member_id'],
                            'agent_id' => $data['agent_id'],
                            'money' => $data['amount'],
                            'remark' => '',
                            'source' => SourceEnum::MEMBER_COMMISSION_WITHDRAWAL,
                            'source_id' => $id,
                            'pay_type' => WithdrawalTypeEnum::BALANCE,
                            'member_balance' => $memberBalance1,
                            'balance' => (float)$memberBalance1 + (float)$data['amount'],
                            'describe' => '佣金提现',
                            'type'=>1,
                            'create_time' => time(),
                            'update_time' => time(),
                        ]);
                    }
                }

            }
            if(!$res){
                Db::rollback();
                return false;
            }
            if($data['type'] === WithdrawalTypeEnum::COMMISSION) {
                app(MemberService::class)->deleteCacheDetail($data['member_id']);
            }
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }
    public function handle($data){
        Db::startTrans();
        try {
            $detail = $this->dao->model->where(['id' => $data['id']])->find();
            $res = $detail->save([
                'status' => $data['status'],
                'update_time' => time(),
            ]);
            #到账及记录流水
            if($res && $data['status'] == 2){

                $memberCoin = app(MemberCoinService::class)->dao->model->where('member_id',$detail['member_id'])->find();

                if($detail['type']['value'] === WithdrawalTypeEnum::BALANCE){
                    $memberBalance = $memberCoin['balance'];
                    $res = $memberCoin->save(['balance'=>Db::raw('balance+'.($detail['money'] ))]);
                }else{
                    $memberBalance = $memberCoin['commission_balance'];
                    $res = $memberCoin->save(['commission_balance'=>Db::raw('commission_balance+'.($detail['money'] ))]);
                }

                $res =  app(WaterService::class)->create([
                    'member_id' => $detail['member_id'],
                    'agent_id' => $detail['agent_id'],
                    'money' => $detail['money'],
                    'source_id' =>$detail['id'],
                    'source' => $detail['type']['value'] === WithdrawalTypeEnum::BALANCE ? SourceEnum::MEMBER_WITHDRAWAL : SourceEnum::MEMBER_COMMISSION_WITHDRAWAL,
                    'pay_type' => $detail['pay_type']['value'],
                    'member_balance' =>$memberBalance,
                    'balance' => $memberBalance + $detail['money'],
                    'describe' => '提现驳回',
                    'type'=>1
                ]);
            }

            if(!$res){
                Db::rollback();
                return false;
            }
            app(MemberService::class)->deleteCacheDetail( $detail['member_id']);
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }

    public function checkWorkTime(): bool {
        // 获取当前时间
        $currentTime = new DateTime();

        // 设置时区（根据实际情况调整）
        $currentTime->setTimezone(new DateTimeZone('Asia/Shanghai'));

        // 获取当前小时和分钟
        $hour = (int)$currentTime->format('H');
        $minute = (int)$currentTime->format('i');

        // 获取当前是星期几（l 为星期几，1 是星期一，7 是星期日）
        $dayOfWeek = (int)$currentTime->format('N');

        // 判断是否在工作时间（周一至周五 9:00 - 17:00）
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // 周一到周五
            if (($hour > 9 && $hour < 17) || ($hour == 9 && $minute >= 0) || ($hour == 17 && $minute == 0)) {
                return true; // 允许
            }
        }
        return false; // 不允许
    }

    public function checkCommissionTime(): bool
    {
        $today = date('j'); // 'j' 表示月份中的第几天，不带前导零

// 定义允许提现的日子
        $withdrawalDays = [5, 15, 25];

// 判断今天是否在允许提现的日子里
        if (in_array($today, $withdrawalDays)) {
           return true;
        } else {
            return false;
        }
    }
}