<?php
/**
 * @Date: 2025/7/6 17:25
 */
declare(strict_types=1);
namespace app\common\model;

use app\common\enum\EnumFactory;
use app\common\enum\logs\OperateSourceEnum;
use app\common\helper\IpHelper;
use app\common\library\check\HeaderUserAgentLibrary;
use app\common\model\agent\AgentModel;
use app\common\model\member\MemberModel;
use app\common\model\system\AdminModel;
use app\common\traits\ModelTrait;

/**
 * 日志
 * Class OperateLogsModel
 * @package app\common\model
 */
class OperateLogsModel extends BaseModel
{
    use ModelTrait;

    /**
     * 数据表主键
     */
    protected $pk = 'id';

    /**
     * 模型名称
     */
    protected $name = 'operate_logs';


    /**
     * 追加字段
     * @var array
     */
    protected $append = [
        "agent_default"
    ];

    public function member()
    {
        return $this->hasOne(MemberModel::class, 'id', 'source_id')->bind([
            "source_username"=>"username",
            "source_nickname"=>"nickname",
            "source_mobile"=>"mobile",
        ]);
    }
    public function agent()
    {
        return $this->hasOne(AgentModel::class, 'id', 'source_id')->bind([
            "source_username"=>"account",
            "source_nickname"=>"real_name",
            "source_mobile"=>"p_nickname",
        ]);
    }
    public function admin()
    {
        return $this->hasOne(AdminModel::class, 'id', 'source_id')->bind([
            "source_username"=>"account",
            "source_nickname"=>"real_name",
        ]);
    }
    /**
     * 来源获取器
     */
    public function getSourceAttr($value)
    {
        return EnumFactory::instance('logs.operate_source')->getItem($value);
    }
    public function getTypeAttr($value)
    {
        return EnumFactory::instance('logs.operate_type')->getItem($value);
    }

    public function getIpAttr($value,$data){
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

    public function getParamDataAttr($value)
    {
        if(!empty($value)){
            return json_decode($value,true);
        }
        return [];
    }

    public function getAgentDefaultAttr($value,$data){
        if(!empty($data['user_agent'])){
            $userAgent = $data['user_agent'];
            return app(HeaderUserAgentLibrary::class)->getInfo($userAgent);
        }
        return [];
    }
}