<?php
/**

 * @Date: 2024/2/22 18:11
 */
declare(strict_types=1);
namespace app\common\model\finance;

use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\model\PaymentModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;
use app\sys\controller\Payment;

/**
 * 用户
 * Class  MemberRechargeModel
 * @package app\common\model\finance
 */
class MemberRechargeModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'finance_member_recharge';

    protected $append = [
        'payment1_name',
        'payment1_nickname',
    ];
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
    public function payment(){
        return $this->hasOne(PaymentModel::class,'id','payment_id')->bind([
            'payment_name'=>'name',
            'payment_nickname'=>'nickname',
        ]);
    }

    public function getPayment1NameAttr($value,$data)
    {
        if(!empty($data['payment_id']) && $data['payment_id'] == -1){
            return '支付宝在线支付';
        }
        return '';
    }
    public function getPayment1NicknameAttr($value,$data)
    {
        if(!empty($data['payment_id']) && $data['payment_id'] == -1){
            return '支付宝在线支付';
        }
        return '';
    }

    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('finance.status')->getItem($value);
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