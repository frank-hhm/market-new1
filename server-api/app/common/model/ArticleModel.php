<?php
/**
 * @Date: 2025/6/28 11:00
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 文章
 * Class ArticleModel
 * @package app\common\model
 */
class ArticleModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'article';

    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }
}