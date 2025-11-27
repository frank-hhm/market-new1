<?php
/**
 * @Date: 2025/6/23 17:10
 */
declare(strict_types=1);
namespace app\websocket\services;

use app\common\services\BaseService;

class PushMarketService extends BaseService
{

    public function pushMarket($pid,array $market,$kTime = 0): void
    {
        $arr = [
            'id' => $market['id'],
            'p' => $market['price'],
            'v' => $market['volume'] ?? 0,
//            't' => $kTime,
//            'ts'=>date('Y-m-d H:i:s',time())
        ];
        TableService::broadcastSubscribe($pid,json_encode(['type' => 'market', 'data' => $arr],JSON_UNESCAPED_UNICODE));
    }
}