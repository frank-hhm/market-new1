<?php
/**
 * @Date: 2025/7/4 13:56
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\ProductCollectModel;

/**
 * 产品收藏
 * Class ProductCollectDao
 * @package app\common\dao
 */
class ProductCollectDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductCollectModel::class;
    }
}