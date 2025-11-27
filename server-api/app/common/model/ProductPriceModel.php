<?php
/**
 * @Date: 2025/6/30 21:48
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\traits\ModelTrait;
use think\facade\Request;

/**
 * 产品价位控制
 * Class ProductPriceModel
 * @package app\common\model
 */
class ProductPriceModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'product_price_set';

    public function product(){
        return $this->hasOne(ProductModel::class,'id','pid');
    }
    public function getExecuteTimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
    public function getCompleteTimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
}