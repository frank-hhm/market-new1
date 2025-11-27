<?php
/**

 * @Date: 2024/2/22 19:18
 */
declare(strict_types=1);
namespace app\common\dao\finance;

use app\common\dao\BaseDao;
use app\common\model\finance\WaterModel;
/**
 * 财务流水
 * Class WaterDao
 * @package app\common\dao\mate
 */
class WaterDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return WaterModel::class;
    }
}