<?php
/**
 * @Date: 2025/3/27 19:28
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\dao\ProductDataDao;
use app\common\exception\CommonException;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;

/**
 * 产品数据
 * Class ProductDataService
 */
class ProductDataService extends BaseService
{
    /**
     * ProductDataService constructor.
     * @param ProductDataDao $dao
     */
    public function __construct(ProductDataDao $dao)
    {
        $this->dao = $dao;
    }
}