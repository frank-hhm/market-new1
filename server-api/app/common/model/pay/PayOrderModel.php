<?php
/**
 * @Date: 2024/11/7 21:04
 */
declare(strict_types=1);
namespace app\common\model\pay;

use app\common\enum\EnumFactory;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 支付表
 * Class PayOrderModel
 * @package app\common\model\pay
 */
class PayOrderModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'pay_order';
}
