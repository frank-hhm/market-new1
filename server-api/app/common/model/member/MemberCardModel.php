<?php
/**
 * @Date: 2025/6/26 0:48
 */
declare(strict_types=1);
namespace app\common\model\member;

use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;

/**
 * 用户
 * Class  MemberCardModel
 * @package app\common\model\member
 */
class MemberCardModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'member_card';

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id')->bind([
            'member_username'=>'username',
            'member_mobile'=>'mobile',
            'member_avatar'=>'avatar',
        ]);
    }
    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }
    /**
     * 状态获取器
     */
    public function getBindStatusAttr($value)
    {
        return EnumFactory::instance('member.bind_status')->getItem($value);
    }
    public function getBindTimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
    public function getHandleTimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
}