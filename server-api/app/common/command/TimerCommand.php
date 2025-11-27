<?php
/**
 * @Date: 2025/3/27 22:55
 */
declare(strict_types=1);
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Events\Event;
use Workerman\Timer;
use Workerman\Worker;

class TimerCommand extends Command
{
    // 定时器句柄/ID
    protected array $timer = [];
    protected function configure()
    {
        // 指令配置

        $this->setName('TimerCommand')
            ->addArgument('status', Argument::REQUIRED, 'start/stop/reload/status/connections')
            ->addOption('d', null, Option::VALUE_NONE, 'daemon（守护进程）方式启动')
            ->setDescription('start/stop/restart 定时任务');
    }

    protected function init(Input $input, Output $output)
    {
        global $argv;
        $argv[1] = $input->getArgument('status') ?: 'start';
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }
    }

    /**
     * 创建定时器
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output): void
    {
        try {
            $this->init($input, $output);
            // 创建定时器任务
            $worker = new Worker();
            $worker->onWorkerStart = [$this, 'start'];
            $worker->runAll();
        }catch (\Exception $e){
            //dump($e);die();
        }
    }

    /**
     * 定时器执行的内容
     */
    public function start(): void
    {
        $this->timer[] = Timer::add(1, function () use (&$task) {
            try {
                go(function () {
                });
            } catch (\Throwable $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
                $this->stop();
            }
        });

        // 每隔n秒执行一次
        $this->timer[] = Timer::add(0.2, function () use (&$task) {
            try {
            } catch (\Throwable $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
                $this->stop();
            }
        });
    }

    /**
     * 停止/删除定时器
     * @return bool
     */
    public function stop()
    {
        $status = true;
        foreach ($this->timer as $timer){
            $_status = Timer::del($timer);
            if($_status === false){
                $status = false;
            }
        }
        return$status;
    }
}