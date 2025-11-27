<?php
/**
 * @Date: 2025/3/27 19:07
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\constants\CacheKeyConstant;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;
use app\common\services\common\PushWebSockQueueService;
use app\websocket\services\PushMessageService;
use app\websocket\utility\RedisQueueUtility;
use EasySwoole\Queue\Job;

/**
 * 产品缓存
 * Class ProductCacheService
 */
class ProductCacheService
{
    /**
     * 删除所有产品缓存
     */
    public function deleteAllCache($id = 0,$isPush = true): bool
    {
        $res = [];
        if($id !== 0){
            $res[] = $this->getProductDetailCache($id,true);
        }
        $res[] = $this->getProductCacheSelect(true);
        if($isPush){
            app(PushWebSockQueueService::class)->createProductMessage(['type'=>'productCache']);
        }
        return ArrayHelper::checkArr($res);
    }

    /**
     * 删除产品缓存
     */
    public function deleteDetailCache($productId = 0): bool
    {
        $cacheKey = CacheKeyConstant::PRODUCT_DETAIL .":{$productId}";
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        if(!$cacheService->has($cacheKey)){
            return true;
        }
        return $cacheService->delete($cacheKey);
    }
    public function getProductCacheSelect($init =  false){
        $cacheKey = CacheKeyConstant::PRODUCT_SELECT;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        $select = $cacheService->get($cacheKey);
        if(empty($select) || $init){
            $select = app(ProductService::class)->dao->model->with(['product_data'])->select()->toArray();
            foreach ($select as &$item){
                $item["is_open"] = (!empty($item['open_time']) && ProductHelper::checkIsOpen($item['open_time']));
                $this->getProductDetailCache($item['id'],true);
            }
            $cacheService->set($cacheKey,$select,60 * 2);
        }
        return $select;
    }

    public function getProductDetailCache($pid,$isInit = false){
        $cacheKey = CacheKeyConstant::PRODUCT_DETAIL.':'.$pid;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        $detail = $cacheService->get($cacheKey);
        if((empty($detail) || $isInit) && !empty($detail = app(ProductService::class)->dao->model->with(['product_data'])->where(['id'=>$pid])->find())){
            $detail = $detail->toArray();
            $detail["is_open"] = (!empty($detail['open_time']) && ProductHelper::checkIsOpen($detail['open_time']));
            $cacheService->set($cacheKey,$detail);
        }
        return $detail;
    }


    public function getProductMarketPriceCache($pid = 0,$isInit = true){
        $cacheKey = CacheKeyConstant::PRODUCT_PRICE.':'.$pid;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        $price = $cacheService->get($cacheKey);
        if(empty($price) && $isInit){
            $proDetail = app(ProductDataService::class)->dao->model->where(['pid'=>$pid])->find();
            if(!empty($proDetail)){
                $price = $proDetail['price'] ?? 0;
            }
            $cacheService->set($cacheKey,$price);
        }
        return $price;
    }
}