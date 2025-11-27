<?php
/**
 * @Date: 2025/7/4 16:30
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\traits\ModelTrait;


/**
 * 产品参数表
 * Class ProductParamsModel
 * @package app\common\model
 */
class ProductParamsModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'product_params';
}