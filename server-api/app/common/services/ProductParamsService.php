<?php
/**
 * @Date: 2025/7/4 16:24
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\constants\CacheKeyConstant;
use app\common\dao\ProductParamsDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\common\CacheService;
use think\facade\Db;

/**
 *
 * Class ProductParamsService
 */
class ProductParamsService extends \app\common\services\BaseService
{
    protected string $cacheKey = 'product:params';

    /**
     * ProductParamsService constructor.
     * @param ProductParamsDao $dao
     */
    public function __construct(ProductParamsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($pid, $exception = true)
    {
        $detail = $this->dao->model->where([
            'pid'=>$pid
        ])->find();
        if (empty($detail)) {
            return $this->createDefault($pid);
        }
        return $detail->toArray();
    }

    public function createDefault($pid)
    {
        $data = [
            'pid'=>$pid,
            'heyue_danwei'=>'',
            'money_danwei'=>'',
            'dian_cha'=>'',
            'baozhengjin_rate'=>'',
            'geye_baozhengjin_rate'=>'',
            "open_time"=>""
        ];
        $this->dao->model->create($data);
        return $data;
    }

    public function updateProductParams($data)
    {
        Db::startTrans();
        try {

            $detail = $this->dao->model->where([
                'pid'=>$data['pid']
            ])->find();
            if(empty($detail)){
                $res[] = $this->createDefault($data['pid']);
            }else{
                $res[] = $detail->save($data);
            }
            $res[] = $this->clearCache($data['pid']);
            // 事务提交
            if (!ArrayHelper::checkArr($res)){
                Db::rollback();
                throw new CommonException('更新失败');
            }
            $res[] = $this->clearCache($data['pid']);
            Db::commit();
            return $res[0];
        }catch (\Exception $e){
            Db::rollback();
            throw new CommonException($e->getMessage());
        }
    }

    public function getCache($productId)
    {
        $cacheKey = CacheKeyConstant::PRODUCT_PARAMS .":{$productId}";
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        $cache = $cacheService->get($cacheKey);
        if(empty($cache)){
            $cache = $this->getDetail($productId);
            $cacheService->set($cacheKey,$cache,60*10);
        }
        return $cache;
    }
    public function clearCache($productId)
    {
        $cacheKey = CacheKeyConstant::PRODUCT_PARAMS .":{$productId}";
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        if(!$cacheService->has($cacheKey)){
            return true;
        }
        return $cacheService->delete($cacheKey);
    }
}