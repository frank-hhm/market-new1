<?php
/**
 * @Date: 2025/6/27 20:43
 */
declare(strict_types=1);

namespace app\common\services\order;

use app\common\dao\order\OrderRobotDao;
use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\member\MemberService;
use think\facade\Db;

/**
 * 挂单记录
 * Class OrderRobotService
 */
class OrderRobotService extends BaseService
{

    /**
     * OrderRobotService constructor.
     * @param OrderRobotDao $dao
     */
    public function __construct(OrderRobotDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter,$exception = true)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail ) {
            if(!$exception){
                return [];
            }
            throw new CommonException('挂单不存在');
        }
        return $detail->toArray();
    }

    /*
     * 获取全部挂单记录
     */
    public function getAllOrderRobot(): array
    {
        return $this->dao->model->where([
            ['status','=',0],
        ])->select()->toArray();
    }

    /*
     * 根据用户ID获取挂单记录
     */
    public function getOrderRobotListByMemberId($memberId){
        $select = $this->dao->model->where('member_id',$memberId)->where('status',0)->order('id', 'desc')->select()->toArray();
        return $select;
    }

    /*
     * 获取挂单列表
     */
    public function getOrderRobotList($status = 0,$param = [] , $agentIds = []){
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $isMoni = false;
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
//            $filter[] = [$tableName.'.agent_id', '=', $param['agent_id']];
            $filter[] = ['agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = ['agent_id','in',$agentIds];
        }
        if(!empty($param['moni']) && $param['moni'] !== 'all' ){
            $isMoni = true;
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        $memberTableName = app(MemberService::class)->dao->model->getTable();
        $list = $this->dao->model->with(['member','agent'])->when(!empty($param['username_like']), function ($query) use ($param , $memberTableName) {
            $query->table($memberTableName)->where(  ['username|mobile','like','%'.$param['username_like'].'%'])->field('id');
        })->when($isMoni, function ($query) use ($memberTableName) {
            $query->table($memberTableName)->where('moni',1)->field('id');
        })->where($filter)->where([
            ['status','=',$status],
        ])->order(['create_time'=>'DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    /*
     * 撤单
     */
    public function cancelOrder($memberId,$orderRobotId){
        Db::startTrans();
        try {
            $this->dao->model->where([
                'member_id' => $memberId,
                'id' => $orderRobotId
            ])->update([
                'status' => 2,
                'close_time' => time(),
            ]);
            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'closeOrder','member_id'=>$memberId]);
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::collback();
            return false;
        }
    }

    /**
     * 后台批量撤单
     */
    public function close($ids){
        Db::startTrans();
        try {
            $this->dao->model->where(
                'id' ,'in', $ids
            )->update([
                'status' => 2,
                'close_time' => time(),
                'remark' => '后台撤单'
            ]);
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::collback();
            return false;
        }
    }
}