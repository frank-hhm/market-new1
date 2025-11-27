<?php
/**
 * @Date: 2025/6/27 15:41
 */
declare(strict_types=1);
namespace app\api\services;

use app\api\services\member\MemberService;
use app\common\constants\CacheKeyConstant;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\agent\AgentService;
use app\common\services\common\CacheService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\WaterService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderLogService;
use app\common\services\order\OrderRobotService;
use app\common\services\order\OrderService as CommonOrderService;
use app\common\services\ProductCacheService;
use think\facade\Db;

/**
 *
 * Class OrderService
 */
class OrderService extends CommonOrderService
{
    public function getDetailApi($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('订单不存在');
        }
        return $detail->toArray();
    }


    public function getOrderList($uid,$params){

        $filter = [];
        $filter[] = ['member_id','=',$uid];
        if(isset($params['ostaus'])){
            $filter[] = ['ostaus','=',$params['ostaus']];
        }
        if(!empty($params['start_date']) && !empty($params['end_date'])){
            $filter[] = ['selltime','>=',StringHelper::_strtotime($params['start_date'] . ' 00:00:00')];
            $filter[] = ['selltime','<=',StringHelper::_strtotime($params['end_date'] . ' 23:59:59')];
        }else{
            $filter[] = ['selltime','>=',StringHelper::_strtotime(date('Y-m-d') . ' 00:00:00')];
            $filter[] = ['selltime','<=',StringHelper::_strtotime(date('Y-m-d') . ' 23:59:59')];
        }
        $list = $this->dao->model->where($filter)->order('selltime DESC')->select()->toArray();

        return $list;
    }

    public function getOrderCountAll($pid = 0,$ostyle = 1){
        return $this->dao->model->where([
            'pid'=>$pid,
            'ostyle'=>$ostyle
        ])->count();
    }
}