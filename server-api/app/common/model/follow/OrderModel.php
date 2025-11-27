<?php
declare(strict_types=1);
namespace app\common\model\follow;

use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 跟单订单
 * Class OrderModel
 * @package app\common\model\follow
 */
class OrderModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'follow_order';
    protected $append = [
        "create_day"
    ];

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
    public function person(){
        return $this->hasOne(PersonModel::class,'id','person_id');
    }
    public function personOrder(){
        return $this->hasMany(OrderModel::class,'person_id','person_id');
    }

    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('follow.order_status')->getItem($value);
    }
    public function getCreateDayAttr($value,$data){
        return StringHelper::registrationDuration(StringHelper::_strtotime($data["create_time"]),true);
    }
}