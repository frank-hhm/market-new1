<?php
/**
 * @Date: 2025/7/4 13:57
 */
declare(strict_types=1);

namespace app\common\services;

use app\common\dao\ProductCollectDao;
use app\common\helper\ArrayHelper;
use app\common\services\member\MemberService;
use think\facade\Db;
use think\facade\Filesystem;
/**
 * 产品收藏
 * Class ProductCollectService
 */
class ProductCollectService extends BaseService
{

    /**
     * ProductCollectService constructor.
     * @param ProductCollectDao $dao
     */
    public function __construct(ProductCollectDao $dao)
    {
        $this->dao = $dao;
    }

    public function getCollectIdsByMemberId($memberId=0): array
    {
        return $this->dao->model->where('member_id',$memberId)->column('pid');
    }

    public function addOptional($productId,$memberId){

        Db::startTrans();
        try {
            $res = [];
            $res[] = app(ProductCollectService::class)->dao->model->create([
                 'pid'=>$productId,
                 'member_id'=>$memberId,
             ]);
            $res[] = app(MemberService::class)->deleteCacheDetail($memberId);
            if (!empty($res) && ArrayHelper::checkArr($res)) {
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

    public function delOptional($productId,$memberId){

        Db::startTrans();
        try {
            $res = [];
            $res[] = app(ProductCollectService::class)->dao->model->where([
                'pid'=>$productId,
                'member_id'=>$memberId,
            ])->delete();
            $res[] = app(MemberService::class)->deleteCacheDetail($memberId);
            if (!empty($res) && ArrayHelper::checkArr($res)) {
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