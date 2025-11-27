<?php
/**
 * @Date: 2025/6/30 21:47
 */
declare(strict_types=1);
namespace app\sys\controller;

use app\common\constants\CacheKeyConstant;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\ProductCacheService;
use app\common\services\ProductDataService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\ProductPriceService;
use think\facade\Cache;
use think\facade\Log;

/**
 * 产品价位控制
 * Class ProductPrice
 * @package app\sys\controller
 */
class ProductPrice extends Base
{
    /**
     * ProductPrice constructor.
     * @param App $app
     * @param ProductPriceService $service
     */
    public function __construct(App $app, ProductPriceService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 产品列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['Price', 0],
            ['execute_time', ''],
            ['complete_time', ''],
        ]);
        $this->success('获取成功',$this->service->getList($data));
    }

    /**
     * 添加产品调控
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['price', 0.00],
            ['execute_time', ''],
            ['complete_time', ''],
            ['type',1]
        ]);
        $data['execute_time'] = StringHelper::_strtotime($data['execute_time']);
        $data['complete_time'] = StringHelper::_strtotime($data['complete_time']);

        if($data['type'] == 2 && $this->service->setProductPriceCache($data['pid'],$data['price'])){
            $this->success('提交插针成功!');
        }


        if ($this->service->createPrice($data)) {
            $this->success('添加成功!');
        }
        $this->error($this->service->getError()?:'添加失败!');
    }
    /**
     * 删除产品调控
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $detail = $this->service->getDetail($id);
        if ($this->service->delete((int)$id)) {
            $this->service->deleteCache($detail['pid']);
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }


    /**
     * 获取产品价格
     * @noAuth(true)
     * @method(GET)
     */
    public function getMklinePrice()
    {
        $data = $this->request->getMore([
            ['pid', 0],
        ]);

        $marketCacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $productNowPrice = $marketCacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$data['pid']);
        $this->success('获取成功',[
            'price'=>$productNowPrice,
        ]);
    }
}