<?php
/**

 * @Date: 2024/2/22 18:12
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\MemberRechargeModel;
/**
 * 用户充值
 * Class MemberRechargeDao
 * @package app\common\dao\mate
 */
class MemberRechargeDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return MemberRechargeModel::class;
    }
}