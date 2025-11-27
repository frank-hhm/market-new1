<?php
/**
 * @Date: 2025/3/2 9:34
 */
declare(strict_types=1);
namespace app\common\model\finance;

use app\common\helper\StringHelper;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\model\system\AdminModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;

/**
 * 佣金日记
 * Class  MemberCommissionWaterModel
 * @package app\common\model\finance
 */
class MemberCommissionWaterModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'member_commission_water';

}