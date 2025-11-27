<?php
/**

 * @Date: 2024/3/7 11:34
 */
declare(strict_types=1);
namespace app\common\model\finance;

use app\common\enum\EnumFactory;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\traits\ModelTrait;

/**
 * 用户提现
 * Class MemberWithdrawalModel
 * @package app\common\model
 */
class MemberWithdrawalModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'finance_member_withdrawal';

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id')->bind([
            'member_username'=>'username',
            'member_mobile'=>'mobile',
            'member_avatar'=>'avatar',
            'bank_child'=>'bank_child',
            'bank_real_name'=>'bank_real_name',
            'bank_name'=>'bank_name',
            'bank_code'=>'bank_code',
            'usdt_card'=>'usdt_card',
        ]);
    }
    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('finance.withdrawal_status')->getItem($value);
    }
    public function getTypeAttr($value)
    {
        return EnumFactory::instance('finance.withdrawal_type')->getItem($value);
    }

    /**
     * 类型获取器
     */
    public function getPayTypeAttr($value)
    {
        return EnumFactory::instance('finance.recharge_pay_type')->getItem($value);
    }
}