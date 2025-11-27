<?php
/**
 * @Date: 2023/12/16 12:41
 */

declare(strict_types=1);

namespace app\common\enum;
use think\Container;
/**
 * 基类
 * Class EnumInstance
 */
abstract class EnumInstance
{

    protected static $data;

    public static $class = [
        'status'=> '\\app\\common\\enum\\StatusEnum',
        'version_status'=> '\\app\\common\\enum\\VersionStatusEnum',
        'member'=> [
            'bind_status'=>'\\app\\common\\enum\\member\\BindStatusEnum'
        ],
        'agent'=> [
            'level'=>'\\app\\common\\enum\\agent\\LevelEnum'
        ],
        'product'=>[
            'market_source'=> '\\app\\common\\enum\\product\\MarketSourceEnum'
        ],
        'finance'=> [
            'recharge_pay_type'=>'\\app\\common\\enum\\finance\\RechargePayTypeEnum',
            'source'=>'\\app\\common\\enum\\finance\\SourceEnum',
            'status'=>'\\app\\common\\enum\\finance\\StatusEnum',
            'withdrawal_status'=>'\\app\\common\\enum\\finance\\WithdrawalStatusEnum',
            'withdrawal_type'=>'\\app\\common\\enum\\finance\\WithdrawalTypeEnum',
            'water_type'=>'\\app\\common\\enum\\finance\\WaterTypeEnum',
        ],
        'order'=> [
            'ostaus'=> '\\app\\common\\enum\\order\\OstausEnum',
            'ostyle'=> '\\app\\common\\enum\\order\\OstyleEnum',
            'sys_ping'=> '\\app\\common\\enum\\order\\SysPingEnum',
            'sell_type'=> '\\app\\common\\enum\\order\\SellTypeEnum',
            'robot_status'=> '\\app\\common\\enum\\order\\RobotStatusEnum',
        ],
        'logs'=> [
            'operate_source'=>'\\app\\common\\enum\\logs\OperateSourceEnum',
            'operate_type'=>'\\app\\common\\enum\\logs\OperateTypeEnum',
        ],
        'follow'=> [
            'revenue_type'=>'\\app\\common\\enum\\follow\\RevenueTypeEnum',
            'order_status'=>'\\app\\common\\enum\\follow\\OrderStatusEnum',
        ],
    ];
    public function __construct(){
    }

    /**
     * 静态实例对象
     * @param array $args
     * @return static
     */
    public static function instance(...$args)
    {
        $class = static::$class;
        if(!empty($args[0])){
            $source = explode('.',$args[0]);
            foreach ($source as $item) {
                $class = $class[$item];
            }
            static::$data = $class::data();
        }
        return Container::getInstance()->make(static::class, $args);
    }

    public static function getAll(){
        $classAll = static::getClass(static::$class);
        $data = [];
        foreach ($classAll as $key => $value) {
            $itemKey = str_replace("\\app\\common\\enum\\","",$value);
            $itemKey = str_replace("\\",".",$itemKey);
            $data[$itemKey] =  $value::data();
        }
        return $data;
    }

    public static function getClass($data){
        $classData = [];
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $classData = array_merge(static::getClass($value),$classData);
            }else{
                $classData[] = $value;
            }
        }
        return $classData;
    }
}

