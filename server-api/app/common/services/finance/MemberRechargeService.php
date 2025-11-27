<?php
/**

 * @Date: 2024/2/22 18:13
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\MemberRechargeDao;

use app\common\enum\EnumFactory;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\StatusEnum;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use app\common\services\PaymentService;
use think\facade\Db;

/**
 * 用户充值
 * Class MemberRechargeService
 */
class MemberRechargeService extends BaseService
{
    /**
     * MemberRechargeService constructor.
     * @param MemberRechargeDao $dao
     */
    public function __construct(MemberRechargeDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $param = [],$agentIds = [],$level = 0): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = $this->getFilter($param,$agentIds,$level);
        $list = $this->dao->model->with(['member','agent','payment'])->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
    public function getSum(array $param = [],$agentIds = [],$level = 0)
    {
        $filter = $this->getFilter($param,$agentIds,$level);
        $payTypeEnum = RechargePayTypeEnum::data();
        $statusEnum = StatusEnum::data();
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
                ])){
                    $count = $this->dao->model->where('pay_type', '=', $k)->where('status', '=', $k1)->where($filter)->sum('money');;
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


    public function getFilter(array $param = [],$agentIds = [],$level = 0)
    {
        $filter = [];
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        if($level >= 1){
            $filter[] = ['status', '<>',2];
        }
        if(isset($param['status']) && $param['status'] != ''){
            $filter[] = ['status', '=', $param['status']];
        }
        if(!empty($param['account']) ){
            $filter[] = ['account', 'like', "%{$param['account']}%"];
        }
        if(!empty($param['username_like']) ){
            $filter[] =  ['member_id', 'in', Db::name('member')->where([['username|mobile|nickname', 'like', "%{$param['username_like']}%"]])->column('id')];
        }
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
            $filter[] = ['agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = ['agent_id','in',$agentIds];
        }
        if(!empty($param['payment_id']) && $param['payment_id'] !== 'all' ){
            $filter[] = ['payment_id', '=', $param['payment_id']];
        }

        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        return $filter;
    }
    /**
     * 获取单前待审核的充值
     */
    public function getMemberRecharge($uid)
    {
        return $this->dao->model->where(
            [
                ['member_id','=',$uid],
                ['status','=',StatusEnum::A],
                ['pay_type','in',[
                    RechargePayTypeEnum::OFFLINE_ALIPAY,
                    RechargePayTypeEnum::OFFLINE_BANK
                ]]
            ]
        )->count();
    }

    /**
     * 新增
     */
    public function createRecharge($source, $data, $describeParam)
    {
        return $this->dao->model->create(array_merge([
            'source' => $source,
            'describe' => vsprintf(EnumFactory::instance('finance.source')->getItem($source)['describe'], $describeParam),
        ], $data));
    }

    public function recharge($data,$uid = 0,$mode = 'inc',$sourceType = SourceEnum::RECHARGE,$adminId = 0,$adminName = ''){

        $money  = $data['money'] ?? 0;
        $usdt  = $data['usdt'] ?? 0;

        if (empty($money) || $money <= 0 || empty($usdt) || $usdt <= 0) {
            $this->error = '请输入正确的金额';
            return false;
        }
        if (in_array($data['pay_type'],[
                RechargePayTypeEnum::OFFLINE_ALIPAY,
                RechargePayTypeEnum::OFFLINE_BANK
            ]) && empty($data['account'])){
            throw new CommonException("账号不能为空");
        }
        if (in_array($data['pay_type'],[
                RechargePayTypeEnum::OFFLINE_USDT,
            ]) && empty($data['account'])){
            throw new CommonException("交易ID不能为空");
        }
        $payType = app(EnumFactory::class)->getItem($data['pay_type']);
        if (empty($payType['value'])){
            throw new CommonException("支付类型错误");
        }

        $member = app(MemberService::class)->dao->model->where('id','=',$uid)->find();
        if(empty($member)){
            throw new CommonException("会员不存在");
        }

        //银联充值做一个限制，单笔只能输入 1-1000U
        if(!empty($data['payment_id'])){
            $payment = app(PaymentService::class)->getDetail($data['payment_id']);
            if(empty($payment)){
                throw new CommonException("支付方式不存在");
            }
            if (($payment['min'] > 0 && $usdt < $payment['min']) ||  ($payment['max'] > 0 && $usdt > $payment['max']) ) {
                throw new CommonException("该渠道充值只能输入".$payment['min']."-".$payment['max']."U");
            }
        }
//        if ($data['pay_type'] == RechargePayTypeEnum::OFFLINE_BANK) {
//            if ($usdt < 1 || $usdt > 1000) {
//                throw new CommonException("银联充值只能输入1-1000U");
//            }
//        }


        if ($mode === 'inc') {
            $modeText = '增加';
        } elseif ($mode === 'dec') {
            $modeText = '减少';
        } else {
            $modeText = '修改';
        }
        $memberBalance = $member['balance'];
        // 更新记录
        $this->transaction(function () use ($data,$member,$memberBalance,$sourceType,$modeText, $adminId,$adminName,$money,$usdt) {
            // 新增充值
            $this->createRecharge($sourceType, [
                'member_id' => $member['id'],
                'agent_id' => $member['agent_id'],
                'money' => $money,
                'usdt' => $usdt,
                'remark' => $data['remark'] ?? '',
                'account' => $data['account'] ?? '',
                'source_id' =>$adminId,
                'pay_type' => $data['pay_type'],
                'member_balance' => $memberBalance,
                'payment_id'=>$data['payment_id'] ?? 0,
                'voucher_url' => $data['voucher_url'] ?? "",
            ], [$adminName,$modeText]);
        });
        return true;

    }



    public function handle($data){
        Db::startTrans();
        try {
            $detail = $this->dao->model->where(['id' => $data['id']])->find();
            #到账及记录流水
            $memberCoin = app(MemberCoinService::class)->dao->model->where(['member_id' => $detail['member_id']])->find();
            $memberBalance = 0;
            $countTurn = 0;
            if($detail['status']['value'] == 0 && !empty($memberCoin) && $data['status'] == 1){
                $res[] = app(MemberCoinService::class)->dao->model->where(['member_id' => $detail['member_id']])->save([
                    'balance'=>Db::raw('balance+'.$detail['usdt'])
                ]);
                $memberBalance = $memberCoin['balance'];
                $res[]  =  app(WaterService::class)->create([
                    'member_id' => $detail['member_id'],
                    'agent_id' => $detail['agent_id'],
                    'money' => $detail['usdt'],
                    'remark' => $detail['remark'] ?? '',
                    'source' =>$detail['source']['value'],
                    'source_id' =>$detail['id'],
                    'pay_type' => $detail['pay_type']['value'],
                    'member_balance' => $memberBalance,
                    'balance' => $memberBalance + $detail['usdt'],
                    'describe' => $detail['describe'],
                    'type'=>1
                ]);
                $memberBalance = $memberBalance + $detail['usdt'];

                #是否首次充 送1次抽奖机会
                #入金抽现金
                #入金500USD以上送2次抽奖机会
                #入金1000USD以上送4抽奖机会
                #以此叠加每500次2次机会

                # 2025-0519 变更
                #1，入金1000USD或得1次抽奖机会
                #2，以此叠加每入金1000USD可获得1次抽奖机会
                #3，获得抽奖机会后，如未进行抽奖的用户，抽奖机会将在48小时后清零抽奖次数
                #4，活动最终解释权归香港盛世景期货公司所有
                $countTurn = StringHelper::calculateDrawChances1($detail['usdt']);
                if($countTurn > 0){
                    $res[] = app(MemberService::class)->dao->model->where(['id' => $detail['member_id']])->save([
                        'turntable'=>Db::raw('turntable+'.$countTurn)
                    ]);
                }
            }

            $res[] = $detail->save([
                'turntable'=>$countTurn,
                'status' => $data['status'],
                'update_time' => time(),
            ]);
            $res[] = app(MemberService::class)->deleteCacheDetail( $detail['member_id']);
            if (!empty($res) && ArrayHelper::checkArr($res)){
                Db::commit();
                return true;
            }
            Db::rollback();
            return false;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }
}