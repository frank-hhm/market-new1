<?php
/**
 * @Date: 2025/3/27 19:27
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\ProductDataModel;

/**
 * 产品
 * Class ProductDataDao
 * @package app\common\dao\mate
 */
class ProductDataDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductDataModel::class;
    }
}