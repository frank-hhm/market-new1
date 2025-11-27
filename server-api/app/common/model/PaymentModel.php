<?php
/**
 * @Date: 2025/5/13 11:32
 */
declare(strict_types=1);

namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\model\agent\AgentModel;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 收款渠道
 * Class PaymentModel
 * @package app\common\model
 */
class PaymentModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'payment';
    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }
    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }
    /**
     * 类型获取器
     */
    public function getTypeAttr($value)
    {
        return EnumFactory::instance('finance.recharge_pay_type')->getItem($value);
    }

    public function setSettingAttr($value,$data)
    {
        if(!empty($value) && is_array($value)){
            return json_encode($value,JSON_UNESCAPED_UNICODE);
        }
        return $value;
    }

    public function getSettingAttr($value,$data)
    {
        if(!empty($value) && is_string($value)){
            return json_decode($value,true);
        }elseif (empty($value)){
            return [];
        }
        return $value;
    }

}