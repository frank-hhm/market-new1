<?php
/**
 * @Date: 2025/6/27 19:14
 */
declare(strict_types=1);

namespace app\common\model\order;

use app\common\enum\EnumFactory;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 订单
 * Class OrderModel
 * @package app\common\model\order
 */
class OrderLogModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'order_log';

}