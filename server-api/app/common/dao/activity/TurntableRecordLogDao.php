<?php
/**
 * @Date: 2025/2/27 13:49
 */
declare(strict_types=1);
namespace app\common\dao\activity;

use app\common\dao\BaseDao;
use app\common\model\activity\TurntableRecordLogModel;
/**
 * 中奖记录
 * Class TurntableRecordLogDao
 * @package app\common\dao\activity
 */
class TurntableRecordLogDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return TurntableRecordLogModel::class;
    }
}