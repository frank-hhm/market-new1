<?php
/**
 * @Date: 2025/6/25 17:04
 */
declare(strict_types=1);
namespace app\websocket\process;


use app\websocket\services\PushMessageService;
use app\websocket\services\TableService;
use app\websocket\utility\RedisQueueUtility;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Queue\Job;

class QueueProcess extends AbstractProcess
{
    protected function run($arg)
    {
        // TODO: Implement run() method.
        go(function (){
            try {
                RedisQueueUtility::getInstance()->consumer('websocket_message')->listen(function (Job $job){
                    $res = $job->getJobData();
//                    dump($res);
                    if(isset($res['action']) ){
                        $type = $res['type'] ?? 'pong';
                        $data = $res['data'] ?? [];
                        if( $res['action'] == 'push_common_message'){
                            app(PushMessageService::class)->push($type,$data);
                        }elseif( $res['action'] == 'push_product_message'){
                            app(PushMessageService::class)->push($type,$data);
                        }elseif( $res['action'] == 'push_member_message'){
                            $uid = $res['member_id'];
                            app(TableService::class)->broadcastMember($uid,json_encode(['type' =>$type, 'data' => $data],JSON_UNESCAPED_UNICODE));
                        }
                    }
                });
            }catch (\Exception $e){
                echo $e->getMessage();
            }
        });
    }
}