<?php
/**
 * @Date: 2025/7/7 14:42
 */
declare(strict_types=1);
namespace app\common\model;
use app\common\enum\EnumFactory;
use app\common\helper\StringHelper;
use app\common\traits\ModelTrait;

/**
 * 版本管理
 * Class VersionDataModel
 * @package app\common\model
 */
class VersionDataModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'version_data';

    protected $autoWriteTimestamp = true;

    public function getStatusAttr($value,$data)
    {
        return EnumFactory::instance('version_status')->getItem($value);
    }
    public function getPublishTimeAttr($value){
        if(!empty($value)){
            return StringHelper::formatDatetime($value);
        }
        return $value;
    }
}