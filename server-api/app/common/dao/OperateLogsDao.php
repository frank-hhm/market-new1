<?php
/**
 * @Date: 2025/7/6 17:26
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\dao\BaseDao;
use app\common\model\OperateLogsModel;
/**
 * 日记
 * Class OperateLogsDao
 * @package app\common\dao
 */
class OperateLogsDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return OperateLogsModel::class;
    }
}
