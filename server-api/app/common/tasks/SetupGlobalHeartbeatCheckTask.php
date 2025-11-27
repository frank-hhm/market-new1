<?php
/**
 * @Date: 2025/6/23 17:06
 */
declare(strict_types=1);
namespace app\common\tasks;

use app\common\constants\CacheKeyConstant;
use app\common\constants\StringConstant;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\TableManager;
use Swoole\Coroutine;

class SetupGlobalHeartbeatCheckTask extends AbstractProcess
{
    public mixed $cacheService = null;

    public mixed $logService = null;


    public function run($arg)
    {
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        // TODO: Implement run() method.
        $this->logService->create("【".StringConstant::MARKET_WEBSOCKET_CLIENTS."】【心跳】检测程序开始", true);
        $this->createCheck();
    }
    public function createCheck(): void
    {
        go(function(){
            while (true) {
                Coroutine::sleep(10); // 每10秒检查一次
                $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);
                $currentTime = microtime(true);
                if(!empty($table)){
                    $this->logService->create("【".StringConstant::MARKET_WEBSOCKET_CLIENTS."】【心跳】检测程序,"."当前在线人数：" . $table->count() , true);
//                    echo "当前在线人数：" . $table->count() . "\n";
                    foreach ($table as $fd => $row) {
                        if ($currentTime - $row['last_time'] > 60) {
//                        echo "Client[{$fd}] heartbeat timeout, disconnecting...\n";
//                        if ($server->isEstablished($fd)) {
//                            $server->push($fd, json_encode(['type' => 'disconnect', 'reason' => 'heartbeat timeout']));
//                            $server->close($fd);
//                        }
                            $table->del($fd);
                        }
                    }
                }else{
                    Coroutine::sleep(5);
                }
            }
        });
    }

}