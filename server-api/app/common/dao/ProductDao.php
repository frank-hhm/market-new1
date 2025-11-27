<?php
/**
 * @Date: 2025/2/6 15:31
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\ProductModel;
/**
 * 产品
 * Class ProductDao
 * @package app\common\dao\mate
 */
class ProductDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductModel::class;
    }
}