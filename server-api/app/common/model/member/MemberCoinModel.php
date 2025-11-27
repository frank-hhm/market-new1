<?php
/**
 * @Date: 2025/6/26 0:48
 */
declare(strict_types=1);
namespace app\common\model\member;

use app\common\helper\StringHelper;
use app\common\model\BaseModel;
use app\common\model\system\AdminModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;

/**
 * 用户
 * Class  MemberCoinModel
 * @package app\common\model\member
 */
class MemberCoinModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'member_coin';

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
}