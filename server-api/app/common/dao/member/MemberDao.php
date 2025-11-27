<?php
/**
 * @Date: 2025/6/26 0:51
 */
declare(strict_types=1);
namespace app\common\dao\member;

use app\common\dao\BaseDao;
use app\common\model\member\MemberModel;
/**
 * 用户
 * Class MemberDao
 * @package app\common\dao\member
 */
class MemberDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberModel::class;
    }

}
