<?php
/**
 * @Date: 2025/6/28 11:01
 */
declare(strict_types=1);

namespace app\common\dao;

use app\common\model\ArticleModel;
/**
 * 文章
 * Class ArticleDao
 * @package app\common\dao\mate
 */
class ArticleDao extends BaseDao
{
    /**
     * 设置模型
     */
    protected function setModel(): string
    {
        return ArticleModel::class;
    }
}