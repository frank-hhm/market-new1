<?php
/**
 * @Date: 2025/2/6 15:31
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\dao\ProductDao;
use app\common\enum\product\MarketSourceEnum;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use think\facade\Db;

/**
 * 产品
 * Class ProductService
 */
class ProductService extends BaseService
{
    /**
     * ProductService constructor.
     * @param ProductDao $dao
     */
    public function __construct(ProductDao $dao)
    {
        $this->dao = $dao;
    }
    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('产品不存在');
        }
        $detail = $detail->toArray();
        $detail['price'] = app(ProductCacheService::class)->getProductMarketPriceCache($detail['id']);
        return $detail;
    }


    public function getProductList($params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['time'][0])&&!empty($params['time'][1])){
            $filter[] = ['create_time','>=',strtotime($params['time'][0])];
            $filter[] = ['create_time','<=',strtotime($params['time'][1])+86400];
        }
        if(!empty($params['name'])){
            $filter[] = ['name','like','%'.$params['name'].'%'];
        }
        if(isset($params['status'])&& $params['status'] != '' ){
            $filter[] = ['status','=',$params['status']];
        }
        if(isset($params['cate_id'])&& $params['cate_id'] != '' ){
            $filter[] = ['cate_id','=',$params['cate_id']];
        }
        return $this->dao->model->with(['sector'])->where($filter)->order('sort DESC,create_time DESC')->page($page)->paginate($limit)->toArray();
    }


    /**
     * 新增产品
     */
    public function createProduct($data)
    {
        Db::startTrans();
        try {
            $res = [];
            $productParams = $data['product_params'] ?? [];
            unset($data['product_params']);
            $initPrice = $data['init_price'] ?? 0;
            unset($data['init_price']);
            if(!empty($data['open_time']) && is_array($data['open_time'])){
                $data['open_time'] = json_encode($data['open_time'],JSON_UNESCAPED_UNICODE);
            }
            if(empty($data['id']) ){
                $res[] = $this->dao->model->create($data);
                $proId = $res[0]->id ?? 0;
            }else{
                $proId = $data['id'];
                $data['update_time'] = time();
                $res[] = $this->dao->model->where('id',$proId)->save($data);
            }

            $productDataService = app(ProductDataService::class);
            $proData = [
                'pid'=>$proId,
                'price'=>$initPrice,
                'open_price'=>$initPrice,
                'close_price'=>$initPrice,
                'height_price'=>$initPrice,
                'low_price'=>$initPrice,
                'update_time'=>time()
            ];
            if($productDataService->dao->model->where('pid',$proId)->limit(1)->count() > 0 ){
                if( $initPrice > 0 && !empty($data['market_source']) && $data['market_source'] == MarketSourceEnum::AUTO){
                    $res[] = $productDataService->dao->model->where('pid',$proId)->update($proData);
                }
            }else {
                $proData['id'] = $proId;
                $res[] = $productDataService->dao->model->create($proData);
            }

            if(!empty($productParams)){
                $productParamsService = app(ProductParamsService::class);
                $productParams['pid'] = $proId;
                $res[] = $productParamsService->updateProductParams($productParams);
            }

            $res[] = app(ProductCacheService::class)->deleteAllCache();
            if (!empty($res) && ArrayHelper::checkArr($res)) {
                Db::commit();
                return $proId;
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