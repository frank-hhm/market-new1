<?php
/**
 * @Date: 2025/6/28 11:00
 */
declare(strict_types=1);
namespace app\common\dao;

use app\common\model\BannerModel;
/**
 * 轮播图
 * Class BannerDao
 * @package app\common\dao\mate
 */
class BannerDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return BannerModel::class;
    }
}