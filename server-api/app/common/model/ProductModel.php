<?php
/**
 * @Date: 2025/2/6 15:32
 */
declare(strict_types=1);

namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\helper\ProductHelper;
use app\common\services\ProductDataService;
use app\common\traits\ModelTrait;
use think\facade\Request;
use think\model\concern\SoftDelete;

/**
 * 产品
 * Class ProductModel
 * @package app\common\model
 */
class ProductModel extends BaseModel
{
    use ModelTrait;
    use SoftDelete;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'product';
    protected string $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $append = [
        'is_open'
    ];

    public function sector(){
        return $this->hasOne(ProductSectorModel::class, 'id','sector_id')->bind([
            'sector_name'=>'sector_name'
        ]);
    }
    public function productData(){
        return $this->hasOne(ProductDataModel::class, 'pid','id')->bind([
            'price'=>'price',
            'open_price'=>'open_price',
            'close_price'=>'close_price',
            'height_price'=>'height_price',
            'low_price'=>'low_price',
            'diff'=>'diff',
            'diff_rate'=>'diff_rate',
            'last_close'=>'last_close',
            'volume'=>'volume',
        ]);
    }


    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }
    public function getMarketSourceAttr($value)
    {
        return EnumFactory::instance('product.market_source')->getItem($value);
    }

    public function getIsOpenAttr($value,$data): bool
    {
        return !empty($data['open_time']) && ProductHelper::checkIsOpen($data['open_time']);
    }

    public function getOpenTimeAttr($value)
    {
        if(!empty($value)){
            return json_decode($value,true);
        }
        return [];
    }
}