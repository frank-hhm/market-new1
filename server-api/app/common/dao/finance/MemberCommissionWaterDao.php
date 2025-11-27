<?php
/**
 * @Date: 2025/3/2 9:35
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\MemberCommissionWaterModel;
/**
 * 佣金日记
 * Class MemberCommissionWaterDao
 * @package app\common\dao\mate
 */
class MemberCommissionWaterDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberCommissionWaterModel::class;
    }
}