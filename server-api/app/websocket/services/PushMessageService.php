<?php
/**
 * @Date: 2025/6/25 16:33
 */
declare(strict_types=1);

namespace app\websocket\services;

use app\common\constants\CacheKeyConstant;
use app\common\services\BaseService;
use EasySwoole\EasySwoole\ServerManager;

class PushMessageService extends BaseService
{

    public function push($type = 'pong',array $data = []): void
    {
        $message = [];
        $messageType = $type;
        switch ($type){
            case 'pong':
                $message = [
                    'time' => time()
                ];
                break;
            case 'productCache':
                $message = [
                    'product' => '1'
                ];
                break;
        }
        TableService::broadcast(json_encode(['type' =>$messageType, 'data' => $message],JSON_UNESCAPED_UNICODE));
    }
}