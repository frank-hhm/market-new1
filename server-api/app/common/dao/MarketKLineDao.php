<?php
/**
 * @Date: 2025/3/28 0:22
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\MarketKLineModel;
/**
 * 产品k线数据
 * Class MarketKLineDao
 * @package app\common\dao\mate
 */
class MarketKLineDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MarketKLineModel::class;
    }
}