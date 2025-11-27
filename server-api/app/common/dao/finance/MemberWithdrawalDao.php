<?php
/**

 * @Date: 2024/3/7 11:37
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\MemberWithdrawalModel;
/**
 * 用户提现
 * Class MemberWithdrawalDao
 * @package app\common\dao\mate
 */
class MemberWithdrawalDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberWithdrawalModel::class;
    }
}