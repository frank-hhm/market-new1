<?php
/**
 * @Date: 2025/6/28 11:00
 */
declare(strict_types=1);
namespace app\common\dao\follow;

use app\common\dao\BaseDao;
use app\common\model\follow\PersonModel;
/**
 * 交易员
 * Class PersonDao
 * @package app\common\dao\follow
 */
class PersonDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return PersonModel::class;
    }
}