<?php
/**
 * @Date: 2025/6/27 20:43
 */
declare(strict_types=1);
namespace app\common\dao\order;

use app\common\dao\BaseDao;
use app\common\model\order\OrderRobotModel;
/**
 * 挂单记录
 * Class OrderRobotDao
 * @package app\common\dao\order
 */
class OrderRobotDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return OrderRobotModel::class;
    }
}