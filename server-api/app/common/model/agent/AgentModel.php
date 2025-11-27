<?php
/**
 * @Date: 2025/6/26 1:51
 */
declare(strict_types=1);
namespace app\common\model\agent;

use app\common\enum\EnumFactory;
use app\common\helper\ArrayHelper;
use app\common\helper\IpHelper;
use  app\common\model\BaseModel;
use app\common\traits\JwtAuthModelTrait;
use app\common\traits\ModelTrait;
use think\model\concern\SoftDelete;

/**
 * 代理商模型
 * Class AgentModel
 * @package app\common\model\agent
 */
class AgentModel extends BaseModel
{
    use ModelTrait;
    use JwtAuthModelTrait;
    use SoftDelete;

    ## 数据表主键
    protected $pk = 'id';

    ## 模型名称
    protected $name = 'agent';
    protected string $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    protected $type = [
        'ratio' => 'float',
        'balance' => 'float',
        'ratio_fee' => 'float',
    ];
    public int $minLevel = 1;

    public int $maxLevel = 3;

    /**
     * 时间获取器
     */
    public function getLastTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /**
     * 权限获取器
     */
    public function getRolesAttr($value)
    {
        return ArrayHelper::arrayItemToInt(!empty($value)?explode(',', $value):[]);
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
    public function getLevelAttr($value)
    {
        return EnumFactory::instance('agent.level')->getItem($value);
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
}
