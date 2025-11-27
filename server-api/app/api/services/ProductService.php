<?php
/**
 * @Date: 2025/6/25 15:52
 */
declare(strict_types=1);
namespace app\api\services;

use app\common\constants\CacheKeyConstant;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;
use app\common\services\MarketKLineService;
use app\common\services\order\OrderLogService;
use app\common\services\ProductCacheService;
use app\common\services\ProductService as CommonProductService;
use think\db\Raw;
use think\facade\Db;

/**
 *
 * Class ProductService
 */
class ProductService extends CommonProductService
{


    public function getKLineHomeData($type = 15,$limit = 10)
    {
        $marketKLineService = app(MarketKLineService::class);
        $productSelect = app(ProductCacheService::class)->getProductCacheSelect();
        $productData = [];
        $marketService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $orderLogService = app(OrderLogService::class);
        $orderService = app(OrderService::class);
        foreach ($productSelect as $item){
            if($item['status']['value'] !== 1){
                continue;
            }
            if($item['is_hot'] != 1){
                continue;
            }
            //验证休市
            $isOpen = ProductHelper::checkIsOpen($item['open_time']);
            if (!empty($item['open_time']) && !$isOpen) {
                continue;
            }
            $data = $marketKLineService->dao->model->where([
                ['pid','=',$item['id']],
                ['type','=',$type]
            ])->order('id','desc')
                ->limit($limit)
                ->select()->toArray();
            $echartData = [];
            foreach ($data as $k1 => $v){
                $echartData['time'][$k1] = $v['ktime'];
                $echartData['data'][$k1] = $v['open_price'] - $item['close_price'];
            }
            $item['price'] = $marketService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['id']);
            $newItem = [
                'name' => $item['name'],
                'price' => $item['price'],
                'id' => $item['id'],
                'sort' => $item['sort'],
            ];
            $newItem['k_line'] = $echartData;
            $lin_chu = !empty($item['open_price'] * 1) ? $item['open_price'] : $item['price'];
            if(($item['price'] - $item['open_price']) != 0 && $lin_chu != 0){
                $change_zhi = ($item['price'] - $item['open_price']) / $lin_chu;
            }else{
                $change_zhi = 0;
            }
            $change_juedui = ceil(abs($change_zhi) * 10000) / 10000;
            if ($change_zhi < 0) {
                $change_zhi = $change_juedui * -1;
            }
            $change = sprintf("%.4f", $change_zhi);
            $change_num = sprintf("%.{$item['decimal']}f", $item['price'] - $item['open_price']);
            $newItem['change'] = $change;
            $newItem['change_num'] = $change_num;


            //涨
            $zhang_num_all = $orderService->getOrderCountAll($item['id'],2);

            //跌
            $die_num_all = $orderService->getOrderCountAll($item['id'],1);
            if ($zhang_num_all || $die_num_all){
                $newItem['kaicang_all']['zhang'] = $zhang_num_all;
                $newItem['kaicang_all']['die'] = $die_num_all;
                $newItem['kaicang_all']['zhang_'] = sprintf("%.2f",$zhang_num_all/($zhang_num_all+$die_num_all)*100);
                $newItem['kaicang_all']['die_'] = sprintf("%.2f",100 -$newItem['kaicang_all']['zhang_']);
            }else{
                $newItem['kaicang_all']['zhang'] = 0;
                $newItem['kaicang_all']['die'] = 0;
                $newItem['kaicang_all']['zhang_'] = sprintf("%.2f",0);
                $newItem['kaicang_all']['die_'] = sprintf("%.2f",0);
            }

            $productData[] = $newItem;
        }
        $productData = array_filter($productData);
        $productData = ArrayHelper::arraySort($productData, 'sort', SORT_DESC);
        return [
            'pro_data'=>$productData
        ];

    }

}