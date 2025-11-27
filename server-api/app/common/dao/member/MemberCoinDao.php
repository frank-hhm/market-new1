<?php
/**
 * @Date: 2025/6/26 0:50
 */
declare(strict_types=1);
namespace app\common\dao\member;

use app\common\dao\BaseDao;
use app\common\model\member\MemberCoinModel;
/**
 * 用户钱包
 * Class MemberCoinDao
 * @package app\common\dao\member
 */
class MemberCoinDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberCoinModel::class;
    }
}
