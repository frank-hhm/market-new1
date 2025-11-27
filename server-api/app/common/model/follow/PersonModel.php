<?php
/**
 * @Date: 2025/6/28 11:00
 */
declare(strict_types=1);
namespace app\common\model\follow;

use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 交易员
 * Class PersonModel
 * @package app\common\model\follow
 */
class PersonModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'follow_person';

    protected $append = [
        "create_day"
    ];
    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
    public function personOrder(){
        return $this->hasMany(OrderModel::class,'person_id','id');
    }
    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }

    public function getCreateDayAttr($value,$data){
        return StringHelper::registrationDuration(StringHelper::_strtotime($data["create_time"]));
    }
}