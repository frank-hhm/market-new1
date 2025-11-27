<?php
/**
 * @Date: 2025/6/26 1:01
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\common\dao\member\MemberCoinDao;

use app\common\enum\finance\SourceEnum;
use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\finance\WaterService;
use app\common\services\order\MemberOrderService;
use think\facade\Db;

/**
 * 用户钱包
 * Class MemberCoinService
 */
class MemberCoinService extends BaseService
{
    /**
     * MemberCoinService constructor.
     * @param MemberCoinDao $dao
     */
    public function __construct(MemberCoinDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter, ['member' => ['agent']]);
        if (!$detail) {
            throw new CommonException('用户不存在');
        }
        return $detail->toArray();
    }


    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(isset($param['moni'])  && $param['moni'] !== 'all'){
            $filter[] =  ['moni','=', $param['moni']];
        }
        if(!empty($param['agent_id'])  && $param['agent_id'] !== 'all'){
            $agentIds = is_array($param['agent_id']) ? $param['agent_id'] : [$param['agent_id']];
        }
        $sorter = 'create_time DESC';
        if(!empty($param['table_sorter'])){
            $sorter = json_decode($param['table_sorter'],true);
        }
//        dump($sorter);die;

        $list = $this->dao->model->with(['member' => ['agent']])->hasWhere('member', [
            ['agent_id','in',$agentIds],
            ['username|mobile|nickname','like','%'.$param['username_like'].'%']
        ])->where($filter)->order($sorter)->page($page)->paginate($limit)->toArray();

//        $memberOrderService = app(MemberOrderService::class);
//        foreach ($list['data'] as &$item){
//            $memberTrade = $memberOrderService->getMemberTradeInfo($item['member_id']);
//            $item["yingkui"] = $memberTrade['yingkui_sum'] ?? 0;
//            $item["jingzhi"] = $memberTrade['jingzhi'] ?? 0;
//            $item["keyong"] = $memberTrade['money_keyong'] ?? 0;
//        }
        return $list;
    }

    public function getSum(array $param = [],$agentIds = [], $key = 'balance')
    {
        $filter = [];
        if(!empty($param['agent_id'])  && $param['agent_id'] !== 'all'){
            $agentIds = is_array($param['agent_id']) ? $param['agent_id'] : [$param['agent_id']];
        }
        if(isset($param['moni'])  && $param['moni'] !== 'all'){
            $filter[] =  ['moni','=', $param['moni']];
        }
        return $this->dao->model->hasWhere('member', [
            ['agent_id','in',$agentIds],
            ['username','like','%'.$param['username_like'].'%']
        ])->where($filter)->sum($key);
    }


    public function rechargeToBalance($data,$id,$adminId = 0,$adminName = '')
    {
        if (!isset($data['money']) || $data['money'] === '' || $data['money'] < 0) {
            $this->error = '请输入正确的金额';
            return false;
        }

        $user = $this->dao->model->with(['member'])->where(['id' => $id])->find();
        $userBalance = $user['balance'];
        // 判断充值方式，计算最终金额
        if ($data['mode'] === 'inc') {
            $mode = '增加';
            $type = 1;
            $diffMoney = $data['money'];
            $diffBalance = $data['money'] + $userBalance;
        } elseif ($data['mode'] === 'dec') {
            $mode = '减少';
            $type = 0;
            $diffMoney = -$data['money'];
            $diffBalance = $userBalance - $data['money'];
        } else {
            $mode = '修改';
            $diffMoney =  StringHelper::bcsub((string)$data['money'], $userBalance);
            $diffBalance =$data['money'];
            $type = $diffMoney > 0 ? 1 : 0;
        }
        $source = $data['source'] ?? '';
        // 更新记录
        $this->transaction(function () use ($data,$user, $id, $userBalance, $mode, $diffMoney, $adminId, $adminName,$diffBalance,$type,$source) {
            // 更新账户余额
            $this->dao->model->where(['id' => $id])->inc('balance', (float)$diffMoney)->save();
            // // 新增余额变动记录
            WaterService::instance()->createSave($source, [
                'member_id' => $user['member_id'],
                'agent_id' => $user['member']['agent_id']?? 0,
                'money' => $diffMoney,
                'remark' => $data['remark'],
                'source_id' => $adminId,
                'member_balance' => $userBalance,
                'balance' => $diffBalance,
                'type' => $type,
                'source'=>$source,
                'pay_type'=>'balance'
            ], [$adminName, $mode]);
            app(MemberService::class)->deleteCacheDetail( $user['member_id']);
        });
        return true;
    }
}