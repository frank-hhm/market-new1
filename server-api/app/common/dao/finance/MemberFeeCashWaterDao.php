<?php
/**
 * @Date: 2025/5/3 7:54
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\MemberFeeCashWaterModel;
/**
 * 佣金返现日记
 * Class MemberFeeCashWaterDao
 * @package app\common\dao\finance
 */
class MemberFeeCashWaterDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberFeeCashWaterModel::class;
    }
}