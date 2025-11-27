<?php
/**
 * @Date: 2025/1/10 15:39
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\ProductSectorModel;
/**
 * 产品板块
 * Class ProductTypeDao
 * @package app\common\dao\mate
 */
class ProductSectorDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductSectorModel::class;
    }
}