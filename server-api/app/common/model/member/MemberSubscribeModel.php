<?php
/**
 * @Date: 2025/6/26 0:47
 */
declare(strict_types=1);

namespace app\common\model\member;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;

/**
 * 用户
 * Class  MemberSubscribeModel
 * @package app\common\model\member
 */
class MemberSubscribeModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'member_subscribe';


    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
}