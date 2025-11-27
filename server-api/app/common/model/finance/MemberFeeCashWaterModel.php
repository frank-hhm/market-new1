<?php
/**
 * @Date: 2025/5/3 7:53
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
 * 手续费返现日记
 * Class  MemberFeeCashWaterModel
 * @package app\common\model\finance
 */
class MemberFeeCashWaterModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'finance_member_fee_cash_water';

}