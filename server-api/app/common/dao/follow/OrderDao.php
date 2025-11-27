<?php

declare(strict_types=1);
namespace app\common\dao\follow;

use app\common\dao\BaseDao;
use app\common\model\follow\OrderModel;
/**
 * 跟单订单
 * Class OrderDao
 * @package app\common\dao\follow
 */
class OrderDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return OrderModel::class;
    }
}