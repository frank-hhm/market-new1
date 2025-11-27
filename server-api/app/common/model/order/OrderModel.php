<?php
/**
 * @Date: 2025/6/27 15:52
 */
declare(strict_types=1);
namespace app\common\model\order;

use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\model\ProductModel;
use app\common\traits\ModelTrait;

/**
 * 订单
 * Class OrderModel
 * @package app\common\model
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
    protected $name = 'order';

    protected $append = [];

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id')->bind([
            'member_username'=>'username',
            'member_mobile'=>'mobile',
            'member_avatar'=>'avatar',
        ]);
    }

    public function product(){
        return $this->hasOne(ProductModel::class,'id','pid');
    }
    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }
    /**
     * 动作获取器
     */
    public function getOstausAttr($value)
    {
        return EnumFactory::instance('order.ostaus')->getItem($value);
    }

    public function getOstyleAttr($value)
    {
        return EnumFactory::instance('order.ostyle')->getItem($value);
    }
    public function getSellTypeAttr($value)
    {
        return EnumFactory::instance('order.sell_type')->getItem($value);
    }
    /**
     * 时间修改器
     */
    public function getBuytimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
    /**
     * 时间修改器
     */
    public function getSelltimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
}