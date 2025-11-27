<?php
/**

 * @Date: 2024/3/6 17:58
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\AgentWithdrawalModel;
/**
 * 代理商提现
 * Class AgentWithdrawalDao
 * @package app\common\dao\mate
 */
class AgentWithdrawalDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return AgentWithdrawalModel::class;
    }
}