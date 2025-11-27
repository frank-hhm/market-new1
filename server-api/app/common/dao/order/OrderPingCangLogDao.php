<?php
/**
 * @Date: 2025/6/28 15:50
 */
declare(strict_types=1);
namespace app\common\dao\order;

use app\common\dao\BaseDao;
use app\common\model\order\OrderPingCangLogModel;
/**
 * 订单
 * Class OrderPingCangLogDao
 * @package app\common\dao\mate
 */
class OrderPingCangLogDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return OrderPingCangLogModel::class;
    }
}