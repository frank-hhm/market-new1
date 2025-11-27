<?php
/**
 * @Date: 2025/6/26 1:22
 */
declare(strict_types=1);
namespace app\api\services\follow;

use app\api\services\OrderService;
use app\common\constants\CacheKeyConstant;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;
use app\common\services\follow\PersonService as CommonPersonService;
use app\api\services\follow\OrderService as FollowOrderService;

/**
 *
 * Class PersonService
 */
class PersonService extends CommonPersonService
{

    public function getDetailByPersonId($personId = 0){
        return $this->getDetail([
            "id" => $personId
        ]);
    }
    public function getListApi($params = [],$memberId = 0){
        $filter = [];
        [$page, $limit] = $this->dao->getPageValue();
        $sort = [
            "create_time"=>"DESC"
        ];

        if(!empty($params["sort"])){
            $sort = ["revenue_text"=>"DESC"];
        }
        $list = $this->dao->model->with(['member'])->withCount(['personOrder' => function($query, &$alias) {
            $alias = 'member_count';
        }])->where("status",1)->where($filter)->order($sort)->page($page)->paginate($limit)->toArray();
        $followOrderService = app(FollowOrderService::class);
        foreach ($list["data"] as &$item){
            $item["follow_status"] = $followOrderService->isFollowStatus($memberId,$item["id"]);
        }
        return $list;
    }


    public function getTradingList($personId = 0,$ostaus = 0){
        $personDetail =  $this->getDetail([
            "id" => $personId
        ]);
        if(empty($personDetail) || $personDetail["is_show_order"] == 0){
            return [
                "data"=>[],
                "total"=>0
            ];
        }
        $orderService = app(OrderService::class);
        [$page, $limit] = $this->dao->getPageValue();
        $list = $orderService->dao->model->with(["product"])
            ->where("member_id",$personDetail["member_id"])
            ->where("ostaus",$ostaus)

            ->whereTime('create_time', '>=', '-2 days')
            ->order([ "create_time"=>"DESC"])->page($page)->paginate($limit)->toArray();
        $marketCacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        foreach ($list["data"] as &$item){
            if (empty($item['ploss'])){
                $productNotPrice = $marketCacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['pid']);
                $item['yingkui'] = ProductHelper::orderProfit($productNotPrice,$item["product"]["number"], $item["product"]['money'], $item['buyprice'], $item['onumber']);
            }else{
                $item['yingkui'] = $item['ploss'];
            }
            if($item["product"]["pay_choose"] == 0 || $item["yingkui"] == 0 ){
                $item["revenue"] = 0;
            }else{
                $item["revenue"] =  (float)sprintf("%.2f",$item["product"]["pay_choose"] / ($item["yingkui"] ?? 0) * $item["onumber"]);
            }
        }
        return $list;

    }
}