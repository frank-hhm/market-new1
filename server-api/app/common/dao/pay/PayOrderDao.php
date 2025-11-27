<?php
/**
 * @Date: 2024/11/7 21:06
 */
declare(strict_types=1);
namespace app\common\dao\pay;

use app\common\dao\BaseDao;
use app\common\model\pay\PayOrderModel;

/**
 * Class PayOrderDao
 * @package app\common\dao\pay
 */
class PayOrderDao extends BaseDao
{
    /**
     * 设置模型名
     */
    protected function setModel(): string
    {
        return PayOrderModel::class;
    }

    /**
     * 生成单号
     */
    public function createNo()
    {
        return date('YmdHis') . substr(implode( array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 10);
    }
}
