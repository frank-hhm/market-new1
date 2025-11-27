<?php
/**
 * @Date: 2025/6/25 15:51
 */
declare(strict_types=1);
namespace app\api\controller;
use app\common\services\ProductCacheService;
use app\common\services\ProductCollectService;
use app\common\services\ProductDataService;
use app\common\services\ProductParamsService;
use app\common\services\ProductSectorService;
use think\facade\App;
use app\api\services\ProductService;

/**
 * Class Product
 */
class Product extends \app\api\controller\Base
{
    /**
     * ProductService constructor.
     * @param App $app
     * @param ProductService $service
     */
    public function __construct(App $app, ProductService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }



    /**
     * 获取产品数据
     * @force(false)
     * @method(GET)
     */
    public function getProductSelect(): void
    {
        $proSelect = app(ProductCacheService::class)->getProductCacheSelect();
        $this->success('获取成功',$proSelect);
    }



    /**
     * 获取产品配置
     * @force(false)
     * @method(GET)
     */
    public function getProductConfig(): void
    {
        $list = app(ProductCacheService::class)->getProductCacheSelect();
        $data = [];
        $data["list"] = [];
        foreach ($list as $item){
           if($item["status"]['value'] == 1){
               $_item = $item;
               $productData = app(ProductDataService::class)->dao->model->where(['pid'=>$item['id']])->find();
               if (!empty($productData)){
                   $productData = $productData->toArray();
                   $_item['close_price'] = $productData["close_price"];
                   $_item['height_price'] = $productData["height_price"];
                   $_item['low_price'] = $productData["low_price"];
                   $_item['last_close'] = $productData["last_close"];
                   $_item['price'] = $productData["price"];
               }
               $data["list"][] = $_item;
           }
        }

        $data["collects"] = sysconf("member_default_product|json");
        $data['sector'] = app(ProductSectorService::class)->getProductSectorSelectCache();
        $this->success('获取成功',$data);
    }


    /**
     * 产品详细
     * @noAuth(true)
     * @method(GET)
     */
    public function getProductDetail(){

        $params = $this->request->getMore([
            ['id', 0],
        ]);
        if(empty($params['id'])){
            $this->error('参数错误');
        }

        $data = [];

        $productDetail = app(ProductCacheService::class)->getProductDetailCache($params['id']);

        $this->success('获取成功',$productDetail);
    }



    /**
     * 产品参数详细
     * @force(false)
     * @method(GET)
     */
    public function getProductParamsDetail(){

        $params = $this->request->getMore([
            ['id', 0],
        ]);
        if(empty($params['id'])){
            $this->error('参数错误');
        }

        $data = [];

        $productDetail = app(ProductCacheService::class)->getProductDetailCache($params['id']);
        $data['decimal'] = $productDetail['decimal'];
        $data['selldate'] = $productDetail['yesterday_close_time'] ?? "";
        $data['buy_max'] = $productDetail['buy_max'] ?? "";
        $data['buy_min'] = $productDetail['buy_min'] ?? "";
        $data['pay_choose'] = $productDetail['pay_choose'] ?? "";
        $productParams = app(ProductParamsService::class)->getCache($params['id']);
        $data['heyue_danwei'] = $productParams['heyue_danwei'] ?? "";
        $data['money_danwei'] =  $productParams['money_danwei'] ?? "";
        $data['baozhengjin_rate'] = $productParams['baozhengjin_rate'] ?? "";
        $data['geye_baozhengjin_rate'] = $productParams['geye_baozhengjin_rate'] ?? "";
        $data['dian_cha'] = $productDetail['spread'] ?? "";
        $data['opentimetext'] = $productParams['open_time'] ?? "";
        $this->success('获取成功',$data);
    }

    /**
     * 加入收藏
     * @method(POST)
     */
    public function addOptional()
    {
        $params = $this->request->postMore([
            ['id', 0],
        ]);
        if(empty($params['id'])){
            $this->error('参数错误');
        }
        $productCollect = app(ProductCollectService::class);
        if(
            $productCollect->addOptional($params['id'],$this->uid)){
            $this->success('添加自选成功');
        }
        $this->error($productCollect->getError() ?:'添加自选失败');
    }
    /**
     * 删除收藏
     * @method(PUT)
     */
    public function delOptional()
    {
        $params = $this->request->postMore([
            ['id', 0],
        ]);
        if(empty($params['id'])){
            $this->error('参数错误');
        }
        $productCollect = app(ProductCollectService::class);
        if(
            $productCollect->delOptional($params['id'],$this->uid)){
            $this->success('删除自选成功');
        }
        $this->error($productCollect->getError() ?:'删除自选失败');
    }

}