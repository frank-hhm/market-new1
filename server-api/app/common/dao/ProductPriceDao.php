<?php
/**
 * @Date: 2025/6/30 21:48
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\ProductPriceModel;
/**
 * 产品价位控制
 * Class ProductPriceDao
 * @package app\common\dao\mate
 */
class ProductPriceDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductPriceModel::class;
    }
}