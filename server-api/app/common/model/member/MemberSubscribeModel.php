<?php
/**
 * @Date: 2025/6/26 0:47
 */
declare(strict_types=1);

namespace app\common\model\member;

use app\api\controller\member\Member;
use app\common\helper\IpHelper;
use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;
use think\model\concern\SoftDelete;

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
        return $this->hasOne(Member::class,'id','member_id');
    }
}