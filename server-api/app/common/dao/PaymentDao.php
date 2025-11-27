<?php
/**
 * @Date: 2025/6/27 17:50
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\PaymentModel;

/**
 * 收款渠道
 * Class PaymentDao
 * @package app\common\dao
 */
class PaymentDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return PaymentModel::class;
    }
}