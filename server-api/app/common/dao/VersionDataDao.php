<?php
/**
 * @Date: 2025/7/7 14:43
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\VersionDataModel;
/**
 * 版本管理
 * Class VersionDataDao
 * @package app\common\dao
 */
class VersionDataDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return VersionDataModel::class;
    }
}