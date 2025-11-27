<?php
/**

 * @Date: 2024/3/6 17:57
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\AgentWithdrawalDao;
use app\common\enum\finance\StatusEnum;
use app\common\helper\StringHelper;
use app\common\services\agent\AgentService;
use app\common\services\BaseService;
use think\facade\Db;

/**
 * 代理商提现
 * Class AgentWithdrawalService
 */
class AgentWithdrawalService extends BaseService
{
    /**
     * AgentWithdrawalService constructor.
     * @param AgentWithdrawalDao $dao
     */
    public function __construct(AgentWithdrawalDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $agentKey = 'agent_id';
        $statusKey = 'status';
        if(!empty($param['username_like'])){
            $agentKey = 'AgentWithdrawalModel'.'.'.$agentKey;
            $statusKey = 'AgentWithdrawalModel'.'.'.$statusKey;
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
        if(isset($param['status']) && $param['status'] != ''){
            $filter[] = [$statusKey, '=', $param['status']];
        }
            $list = $this->dao->model->with(['agent'])->when(!empty($param['username_like']), function ($query) use ($param){
            return $query->hasWhere('agent',[['real_name|account', 'like', "%{$param['username_like']}%"]]);
        })->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
    public function getSum(array $param = [],$agentIds = [],$key = 'money')
    {
        $filter = [];
        $agentKey = 'agent_id';
        $statusKey = 'status';
        if(!empty($param['username_like'])){
            $agentKey = 'AgentWithdrawalModel'.'.'.$agentKey;
            $statusKey = 'AgentWithdrawalModel'.'.'.$statusKey;
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
        if(isset($param['status']) && $param['status'] != ''){
            $filter[] = [$statusKey, '=', $param['status']];
        }
        $statusEnum = StatusEnum::data();
        $list = [];
        foreach ($statusEnum as $k1 => $v1){
            $statusData = [
                'title'=>$v1['name'],
                'count' => 0
            ];
            $statusData['count'] =  $this->dao->model->with(['agent'])->when(!empty($param['username_like']), function ($query) use ($param){
                return $query->hasWhere('agent',[['real_name|account', 'like', "%{$param['username_like']}%"]]);
            })->where('status', '=', $k1)->where($filter)->sum('money');;

            $list[] = $statusData;
        }
        return $list;
    }

    /**
     * 新增
     */
    public function createSave($data, $describeParam = '提现申请')
    {
        Db::startTrans();
        try {
            $res = $this->dao->model->create(array_merge([
                'describe' => $describeParam,
            ], $data));
            $id = $res->id;
            #更新余额
            $agentBalance = 0;
            if($res){
                $agentDetail = app(AgentService::class)->dao->model->where('id',$data['agent_id'])->find();
                $agentBalance = $agentDetail['balance'];
                $res = $agentDetail->save(['balance'=>Db::raw('balance-'.$data['amount'])]);
            }
            if($res){
                $res = app(WaterService::class)->dao->model->insert([
                    'member_id' => 0,
                    'agent_id' => $data['agent_id'],
                    'money' => $data['amount'],
                    'remark' => '',
                    'source' => 'agent_withdrawal',
                    'source_id' => $id,
                    'pay_type' => 'balance',
                    'member_balance' => $agentBalance,
                    'balance' => $agentBalance - $data['amount'],
                    'describe' => '代理商提现申请',
                    'type'=>0,
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
            }

            if(!$res){
                Db::rollback();
                return false;
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
                $agentDetail = app(AgentService::class)->dao->model->where('id',$detail['agent_id'])->find();
                $agentBalance = $agentDetail['balance'];
                $res = $agentDetail->save(['balance'=>Db::raw('balance+'.$detail['money'])]);

                $res =  app(WaterService::class)->create([
                    'agent_id' => $detail['agent_id'],
                    'money' => $detail['money'],
                    'source' => 'agent_withdrawal',
                    'source_id' =>$detail['id'],
                    'pay_type' => 'balance',
                    'member_balance' =>$agentBalance,
                    'balance' => $agentBalance + $detail['money'],
                    'describe' => '提现驳回',
                    'type'=>1
                ]);
            }

            if(!$res){
                Db::rollback();
                return false;
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
}