<?php
// App/Task/BroadcastTask.php
namespace app\websocket\tasks;

use app\common\constants\StringConstant;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class BroadcastTask implements TaskInterface
{
    protected mixed $message;
    protected string $tableName;

    public function __construct($message)
    {
        $this->message = $message;
        $this->tableName = StringConstant::MARKET_WEBSOCKET_CLIENTS;
    }

    public function run(int $taskId, int $workerIndex)
    {
        $table = \EasySwoole\Component\TableManager::getInstance()->get($this->tableName);
        $server = ServerManager::getInstance()->getSwooleServer();
        if (!$table) return;

        $maxConcurrency = 1024 * 8; // 控制并发协程数，避免内存爆炸
        $chan = new \Swoole\Coroutine\Channel($maxConcurrency);

        $workers = 0;

        foreach ($table as $fd => $row) {
            $fd = (int)$fd;

            if (!$server->isEstablished($fd)) {
                $table->del($fd);
                continue;
            }

            // 控制并发数量
            while ($workers >= $maxConcurrency) {
                $chan->pop();
                $workers--;
            }
            \Swoole\Coroutine::create(function () use ($server, $fd, $chan, $table) {
                $result = $server->push($fd, $this->message);
                if ($result === false) {
                    $table->del($fd);
                }
                $chan->push(1); // 通知完成
            });

            $workers++;
        }

        // 等待所有协程完成
        for ($i = 0; $i < $workers; $i++) {
            $chan->pop();
        }

        $chan->close();
//        foreach ($table as $fd => $row) {
//            $fd = (int)$fd;
//            // 1. 先检查连接是否有效
//            if (!$server->isEstablished($fd)) {
//                $table->del($fd);
//                continue;
//            }
//            try {
//                $result = $server->push($fd, $this->message);
//                if ($result === false) {
//                    continue;
////                    $table->del($fd);
//                }
//            } catch (\Throwable $throwable) {
//                // 理论上不会进这里，但加了更安心
////                $table->del($fd);
//            }
//        }
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo "BroadcastTask Exception: {$throwable->getMessage()}\n";
    }
}