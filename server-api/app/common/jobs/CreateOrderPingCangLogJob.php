<?php
/**
 * @Date: 2025/6/28 15:50
 */
declare(strict_types=1);
namespace app\common\jobs;
use app\common\services\order\OrderPingCangLogService;
use think\facade\Db;
use think\queue\Job;

/**
 * 订单日记
 */
class CreateOrderPingCangLogJob
{
    # php think queue:listen --queue OrderPingCangLogJob
    /**
     * fire是消息队列默认调用的方法
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
            return true;
        }

        if ($this->doJob($data)) {
            $job->delete();
        }
        return true;
    }

    public function doJob($data)
    {
        try {
            $log = [];

            $log['agent_id'] = $data['agent_id'] ?? 0;
            $log['member_id'] = $data['member_id'] ?? 0;
            $log['pid'] = $data['pid'] ?? 0;
            $log['order_id'] = $data['order_id'] ?? 0;
            $log['selltime'] = $data['selltime'] ?? time();
            $log['sellprice'] = $data['sellprice'] ?? '';
            $log['sell_type'] = $data['sell_type'] ?? '';
            $log['other_data'] = $data['other_data'] ?? '';
            $log['user_jiao_info'] = $data['user_jiao_info'] ?? '';
            $log['order_data'] = $data['order_data'] ?? '';
            if(!empty($log['other_data']) && is_array($log['other_data'])){
                $log['other_data'] = json_encode($log['other_data'],JSON_UNESCAPED_UNICODE);
            }
            if(!empty($log['user_jiao_info']) && is_array($log['user_jiao_info'])){
                $log['user_jiao_info'] = json_encode($log['user_jiao_info'],JSON_UNESCAPED_UNICODE);
            }
            if(!empty($log['order_data']) && is_array($log['order_data'])){
                $log['order_data'] = json_encode($log['order_data'],JSON_UNESCAPED_UNICODE);
            }
            $log['create_time'] = time();

            return app(OrderPingCangLogService::class)->dao->model->insert($log);
        }catch (\Exception $e){
            return false;

        }
    }
}