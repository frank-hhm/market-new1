<?php
/**
 * @Date: 2025/7/4 16:30
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\ProductParamsModel;
/**
 * 产品参数
 * Class ProductParamsDao
 * @package app\common\dao\mate
 */
class ProductParamsDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ProductParamsModel::class;
    }
}