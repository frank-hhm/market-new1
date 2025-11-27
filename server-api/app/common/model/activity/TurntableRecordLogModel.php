<?php
/**
 * @Date: 2025/2/27 13:47
 */
declare(strict_types=1);
namespace app\common\model\activity;

use app\common\enum\EnumFactory;
use app\common\model\BaseModel;
use app\common\model\member\MemberModel;
use app\common\model\system\AdminModel;
use app\common\traits\ModelTrait;

/**
 * 中奖记录
 * Class TurntableRecordLogModel
 * @package app\common\model
 */
class TurntableRecordLogModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'turntable_record_log';

    public function member(){
        return $this->hasOne(MemberModel::class,'id','member_id');
    }
    public function agent(){
        return $this->hasOne(AdminModel::class,'id','agent_id')->bind([
            'agent_real_name'=>'real_name',
            'agent_account'=>'account',
        ]);
    }
}