<?php
/**
 * @Date: 2025/6/26 0:50
 */
declare(strict_types=1);
namespace app\common\dao\member;

use app\common\dao\BaseDao;
use app\common\model\member\MemberSubscribeModel;

/**
 * 用户订阅
 * Class MemberSubscribeDao
 * @package app\common\dao\member
 */
class MemberSubscribeDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberSubscribeModel::class;
    }
}
