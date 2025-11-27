<?php
/**
 * @Date: 2025/6/27 15:51
 */
declare(strict_types=1);
namespace app\common\dao\order;

use app\common\dao\BaseDao;
use app\common\model\order\OrderModel;

/**
 * 订单
 * Class OrderDao
 * @package app\common\dao\mate
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