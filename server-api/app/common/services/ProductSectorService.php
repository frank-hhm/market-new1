<?php
/**
 * @Date: 2025/1/10 15:23
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\constants\CacheKeyConstant;
use app\common\dao\ProductSectorDao;
use app\common\exception\CommonException;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;

/**
 * 产品板块
 * Class ProductSectorService
 */
class ProductSectorService extends BaseService
{
    /**
     * ProductSectorService constructor.
     * @param ProductSectorDao $dao
     */
    public function __construct(ProductSectorDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('板块不存在');
        }
        return $detail->toArray();
    }

    public function getList(array $params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $list = $this->dao->model->where($filter)->order(['sort'=>'DESC','id'=>'DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    public function getSelect(): array
    {
        $filter = [];
        $list = $this->dao->model->where($filter)->order(['sort'=>'DESC','id'=>'DESC'])->select()->toArray();
        return $list;
    }

    public function getProductSectorSelectCache($init = false){
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
        $types = $cacheService->get(CacheKeyConstant::PRODUCT_SECTOR);
        if(empty($types) || $init){
            $types = $this->dao->model->order(['sort'=>'DESC','id'=>"DESC"])->select()->toArray();
            $cacheService->set(CacheKeyConstant::PRODUCT_SECTOR,$types);
        }
        return $types;
    }
}