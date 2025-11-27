<?php
/**
 * @Date: 2025/1/10 15:27
 */
declare(strict_types=1);
namespace app\common\model;
use app\common\traits\ModelTrait;

/**
 * 产品板块
 * Class ProductSectorModel
 * @package app\common\model
 */
class ProductSectorModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'product_sector';
}