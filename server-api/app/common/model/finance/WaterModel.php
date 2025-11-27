<?php
/**

 * @Date: 2024/2/22 19:17
 */
declare(strict_types=1);
namespace app\common\model\finance;

use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;

/**
 * 用户
 * Class  WaterModel
 * @package app\common\model\finance
 */
class WaterModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'finance_water';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $autoWriteTimestamp = 'int';
    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }
    /**
     * 类型获取器
     */
    public function getTypeAttr($value)
    {
        return EnumFactory::instance('finance.water_type')->getItem($value);
    }
    /**
     * 类型获取器
     */
    public function getPayTypeAttr($value)
    {
        return EnumFactory::instance('finance.recharge_pay_type')->getItem($value);
    }
    /**
     * 来源获取器
     */
    public function getSourceAttr($value)
    {
        return EnumFactory::instance('finance.source')->getItem($value);
    }
}