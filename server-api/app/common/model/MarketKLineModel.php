<?php
/**
 * @Date: 2025/3/28 0:21
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 产品数据
 * Class MarketKLineModel
 * @package app\common\model
 */
class MarketKLineModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'market_k_line';
}