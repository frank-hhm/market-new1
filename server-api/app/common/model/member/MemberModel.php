<?php
/**
 * @Date: 2025/6/26 0:47
 */
declare(strict_types=1);

namespace app\common\model\member;

use app\common\helper\IpHelper;
use app\common\helper\StringHelper;
use app\common\model\agent\AgentModel;
use app\common\model\BaseModel;
use app\common\traits\ModelTrait;
use app\common\enum\EnumFactory;
use think\model\concern\SoftDelete;

/**
 * 用户
 * Class  MemberModel
 * @package app\common\model\member
 */
class MemberModel extends BaseModel
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
    protected $name = 'member';
    protected string $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $type = [
        'balance'       => 'float',
    ];

    protected $append = [
        'username_text',
        'people_level',
        'people_progress',
        "create_day"
    ];

    public function agent(){
        return $this->hasOne(AgentModel::class,'id','agent_id');
    }


    public function peopleDetail(){
        return $this->hasOne(MemberModel::class,'id','people_id');
    }


    public function coin(){
        return $this->hasOne(MemberCoinModel::class,'member_id','id')->bind([
            'balance'=>'balance',
            'yingkui'=>'yingkui',
            'commission_balance'=>'commission_balance',
            'commission_total'=>'commission_total',
            "fee_cash_commission_total"=>"fee_cash_commission_total"
        ]);
    }
    /**
     * 状态获取器
     */
    public function getStatusAttr($value)
    {
        return EnumFactory::instance('status')->getItem($value);
    }
    /**
     * 状态获取器
     */
    public function getBindStatusAttr($value)
    {
        return EnumFactory::instance('member.bind_status')->getItem($value);
    }

    public function getUsernameTextAttr($value, $data)
    {
        return StringHelper::maskName($data['username']);
    }
    public function getPeopleLevelAttr($value, $data)
    {
        $level = 1;
        if(!empty($data['people_tui'])){
            if($data['people_tui'] >= 30){
                $level = 10;
            }elseif ($data['people_tui'] >= 10){
                $level = 9;
            }elseif ($data['people_tui'] >= 5){
                $level = 6;
            }elseif ($data['people_tui'] >= 1){
                $level = 3;
            }
        }
        // $level = 10;
        return $level;
    }
    public function getPeopleProgressAttr($value, $data)
    {
        // 根据邀请人数确定进度百分比和返佣比例
        $invitedCount = $data['people_tui'] ?? 0;
        //$invitedCount = 30;

        $differ = 0;
        $differLevel = 1;
        $people = 0;
        $rate = 0;
        if ($invitedCount == 0) {
            $progress = 0; // 未邀请任何人的情况
            $differLevel =  3;
        } elseif ($invitedCount < 6) {
            $progress = round($invitedCount / 5 * 100); // 邀请1-5人之间的进度（线性插值到10%）
            $differ =  5 - $invitedCount;
            $differLevel =  6;
            $people = 1;
            $rate = 10;
        } elseif ($invitedCount < 21) {
            $progress = round($invitedCount / 30 * 100); // 邀请21-60人之间的进度（从30%线性增加到40%）
            $differ =  30 - $invitedCount;
            $differLevel =  10;
            $people = 10;
            $rate = 20;
        } else {
            $progress = 100; // 超过30人，固定在60%，此处假设没有更高层级或额外奖励
            $rate = 60;
            $people = 30;
        }

        // 确保进度不超过100%
        if ($progress > 100) {
            $progress = 100;
        }
        return [
            'progress' => $progress,
            'differ' => $differ,
            'differ_level' => $differLevel,
            'people' => $people,
            'rate' => $rate,
        ];
    }

    /**
     * 时间获取器
     */
    public function getLastTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public function getLastIpAttr($value,$data){
        if(!empty($value)){
            return [
                'text' => IpHelper::getIPCountry($value),
                'value' => $value
            ];
        }
        return [
            'text' => '',
            'value' => $value
        ];
    }
    public function getCreateDayAttr($value,$data){
        return StringHelper::registrationDuration(StringHelper::_strtotime($data["create_time"]));
    }
}