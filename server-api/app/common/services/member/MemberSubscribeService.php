<?php
/**
 * @Date: 2025/6/26 1:01
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\common\dao\member\MemberSubscribeDao;

use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use think\facade\Db;

/**
 * 用户订阅
 * Class MemberSubscribeService
 */
class MemberSubscribeService extends BaseService
{
    /**
     * MemberSubscribeService constructor.
     * @param MemberSubscribeDao $dao
     */
    public function __construct(MemberSubscribeDao $dao)
    {
        $this->dao = $dao;
    }

    public function isExists($filter){
        return $this->dao->model->where($filter)->limit(1)->count();;
    }



    public function createSubscribe($memberId = 0,$params = []): bool
    {
        Db::startTrans();
        try {
            $data['member_id'] = $memberId;
            $data['source_id'] = $params["source_id"] ?? 0;
            $data['source'] = $params["source"] ?? "default";
            $res[] = $this->dao->create($data);
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

    public function deleteSubscribe($memberId = 0,$params = []): bool
    {
        Db::startTrans();
        try {
            $data['member_id'] = $memberId;
            $data['source_id'] = $params["source_id"] ?? 0;
            $data['source'] = $params["source"] ?? "default";
            $res[] = $this->dao->model->where($data)->delete();
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

}