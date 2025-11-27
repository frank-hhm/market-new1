<?php
/**
 * @Date: 2025/6/30 21:47
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\constants\CacheKeyConstant;
use app\common\dao\ProductPriceDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\common\CacheService;
use Psr\SimpleCache\InvalidArgumentException;
use think\facade\Db;

/**
 * 产品价位控制
 * Class ProductPriceService
 */
class ProductPriceService extends BaseService
{
    /**
     * ProductPriceService constructor.
     * @param ProductPriceDao $dao
     */
    public function __construct(ProductPriceDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('不存在');
        }
        return $detail->toArray();
    }

    public function createPrice(array $data): bool
    { Db::startTrans();
        try {
            $res = [];
//            dump($data);die;
            $res[] = $this->dao->model->insert($data);
            $res[] = $this->deleteCache($data['pid']);
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

    public function deleteCache($pid): bool
    {
        try {
            $cacheHouTag = CacheKeyConstant::PRODUCT_SET_PRICE_HOU;
            $lastBatchCacheKey = $cacheHouTag.":last_batch:";
            $batchNumCacheKey = $cacheHouTag.":batch_num:";
            //缓存是否已分配变动金额
            $isHouBatchKey = $cacheHouTag.":is_hou_batch:";
            //缓存计算的数据
            $houDataCacheKey = $cacheHouTag.":data:";
            $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
            $cacheService->delete($lastBatchCacheKey.$pid);
            $cacheService->delete($batchNumCacheKey.$pid);
            $cacheService->delete($isHouBatchKey.$pid);
            $cacheService->delete($houDataCacheKey.$pid);
            $cacheService->delete($cacheHouTag.":detail:".$pid);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /*
     * 插针缓存
     */
    public function setProductPriceCache($pid = 0,$price = 0){
        $cacheKey = CacheKeyConstant::PRODUCT_SET_PRICE_PIN.':'.$pid;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        return $cacheService->set($cacheKey,$price,61);
    }

    public function getProductPriceCache($pid,$isInit = false){
    }

    public function getList(array $params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['pid'])){
            $filter[] = ['pid','=',$params['pid']];
        }
        $list = $this->dao->model->with(['product'])->where($filter)
            ->order(['id DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

}