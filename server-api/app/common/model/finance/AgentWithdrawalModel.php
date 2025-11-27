<?php
/**

 * @Date: 2024/3/6 17:56
 */
declare(strict_types=1);
namespace app\common\model\finance;

use app\common\enum\EnumFactory;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 代理商提现
 * Class AgentWithdrawalModel
 * @package app\common\model
 */
class AgentWithdrawalModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'finance_agent_withdrawal';

    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }

    public function getStatusAttr($value)
    {
        return EnumFactory::instance('finance.withdrawal_status')->getItem($value);
    }
}