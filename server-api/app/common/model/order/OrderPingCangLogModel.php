<?php
/**
 * @Date: 2025/6/28 15:51
 */
declare(strict_types=1);
namespace app\common\model\order;

use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 订单
 * Class OrderPingCangLogModel
 * @package app\common\model
 */
class OrderPingCangLogModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'order_ping_cang_log';

}