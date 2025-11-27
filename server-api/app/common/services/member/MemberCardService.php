<?php
/**
 * @Date: 2025/6/26 1:01
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\common\dao\member\MemberCardDao;

use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\WaterService;
use think\facade\Db;

/**
 * 用户认证
 * Class MemberCardService
 */
class MemberCardService extends BaseService
{
    /**
     * MemberCardService constructor.
     * @param MemberCardDao $dao
     */
    public function __construct(MemberCardDao $dao)
    {
        $this->dao = $dao;
    }


    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail ) {
            throw new CommonException('认证信息不存在');
        }
        return $detail->toArray();
    }

    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $asName = $this->dao->model->getTable();

        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
            $filter[] = [$asName.'.agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = [$asName.'.agent_id','in',$agentIds];
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        if(!empty($param['card_like'])){
            $filter[] = [$asName.'.real_name|'.$asName.'.card_number', 'like','%'.$param['card_like'].'%'];
        }
        $list = $this->dao->model->with(['member','agent'])->where($filter)
            ->when(!empty($param['username_like']), function ($query) use ($param){
                $query->hasWhere('member',[
                    ['username|mobile', 'like', '%'.$param['username_like'].'%']
                ]);
            })->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }


    /**
     * 认证
     */
    public function createSave($id, array $data): bool
    {
        Db::startTrans();
        try {
            $data['member_id'] = $id;
            $data['bind_status'] = 2;
            $res[] = $this->dao->create($data);
            $res[] = app(MemberService::class)->dao->model->where(['id' => $id])->update([
                'bind_status' => 2,
                'card_number' => $data['card_number'],
                'real_name' => $data['real_name'],
            ]);
            $res[] = app(MemberService::class)->deleteCacheDetail($id);
            if(!ArrayHelper::checkArr($res)){
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

    /**
     * 修改认证
     */
    public function updateCard($id, array $data): bool
    {
        Db::startTrans();
        try {
            $detail = $this->dao->model->where('id',$id)->find();
            $res[] = $detail->save($data);
            $res[] = app(MemberService::class)->dao->model->where(['id' => $detail['member_id']])->update([
                'card_number' => $data['card_number'],
                'real_name' => $data['real_name'],
                'update_time' => time(),
            ]);
            $res[] = app(MemberService::class)->deleteCacheDetail($detail['member_id']);
            if(!ArrayHelper::checkArr($res)){
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
            $res[] = $detail->save([
                'bind_status' => $data['status'],
                'handle_time' => time(),
                'bind_time' => time(),
            ]);
            $member = app(MemberService::class)->dao->model->with(['coin'])->where(['id' => $detail['member_id']])->find();

            $res[] = $member->save(['bind_status' => $data['status'],'bind_time'=>time()]);

            $res[] = app(MemberService::class)->deleteCacheDetail($detail['member_id']);

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