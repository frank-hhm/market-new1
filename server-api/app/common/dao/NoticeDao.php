<?php
/**
 * @Date: 2025/6/28 11:01
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\NoticeModel;
/**
 * 公告
 * Class NoticeDao
 * @package app\common\dao\mate
 */
class NoticeDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return NoticeModel::class;
    }
}