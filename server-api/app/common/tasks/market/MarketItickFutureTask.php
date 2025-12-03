<?php
/**
 * @Date: 2025/7/4 16:37
 */
declare(strict_types=1);

namespace app\common\tasks\market;

use app\common\constants\CacheKeyConstant;
use app\common\enum\product\MarketSourceEnum;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use EasySwoole\Component\Process\AbstractProcess;

class MarketItickFutureTask  extends AbstractProcess
{

    //外汇
    public string $host = "api.itick.org";

    public string $path = "/future";

    public string $typeStr = "US";

    public mixed $cacheService = null;

    public mixed $logService = null;

    public string $cacheKey = '';
    public function run($arg)
    {
        // TODO: Implement run() method.
        $runTime = 1000 * 32;
        $marketSource = MarketSourceEnum::ITICK_FUTURE;
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        $this->cacheKey = CacheKeyConstant::MARKET_ITICK_FUTURE_STATUS;
        $taskName = "【Itick期货】";
        $this->logService->create($taskName."程序开始",true);
        sleep(2);
        app(ItickCommon::class)->getRun($taskName,$marketSource,$this->host, $this->path,$this->cacheKey,$this->typeStr);
        $this->addTick($runTime, function () use ($taskName,$marketSource){
            $autoMarket = $this->cacheService->get( $this->cacheKey);
            if (null == $autoMarket || $autoMarket == 0) {
                app(ItickCommon::class)->getRun($taskName,$marketSource,$this->host,  $this->path,$this->cacheKey,$this->typeStr);
            }
        });
    }
}