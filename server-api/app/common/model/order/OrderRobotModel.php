<?php
/**
 * @Date: 2025/6/27 20:42
 */
declare(strict_types=1);
namespace app\common\model\order;

use app\common\enum\EnumFactory;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 挂单记录
 * Class OrderRobotModel
 * @package app\common\model\order
 */
class OrderRobotModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'order_robot';

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }

    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }

    public function getStatusAttr($value)
    {
        return EnumFactory::instance('order.robot_status')->getItem($value);
    }
    public function getOstyleAttr($value)
    {
        return EnumFactory::instance('order.ostyle')->getItem($value);
    }


}