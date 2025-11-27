<?php
/**
 * @Date: 2025/6/27 19:14
 */
declare(strict_types=1);
namespace app\common\dao\order;

use app\common\dao\BaseDao;
use app\common\model\order\OrderLogModel;
/**
 * 订单
 * Class OrderLogDao
 * @package app\common\dao\order
 */
class OrderLogDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return OrderLogModel::class;
    }
}