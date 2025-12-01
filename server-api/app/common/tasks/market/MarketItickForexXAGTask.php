<?php


namespace app\common\tasks\market;

use app\common\constants\CacheKeyConstant;
use app\common\enum\product\MarketSourceEnum;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use EasySwoole\Component\Process\AbstractProcess;

class MarketItickForexXAGTask  extends AbstractProcess
{

    //外汇
    public string $host = "api.itick.org";

    public string $path = "/fws";

    public string $typeStr = "$GB";

    public mixed $cacheService = null;

    public mixed $logService = null;

    public string $cacheKey = '';

    public int $proId = 0;
    public function run($arg)
    {
        // TODO: Implement run() method.
        $runTime = 1000 * 30;
        $marketSource = MarketSourceEnum::ITICK_FOREX_AXG;
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);

        $this->cacheKey = CacheKeyConstant::MARKET_ITICK_FOREX_XAG_STATUS;
        $taskName = "【Itick外汇白银】";
        $this->logService->create($taskName."程序开始",true);
        app(ItickCommon::class)->getRun($taskName,$marketSource,$this->host, $this->path,$this->cacheKey,$this->typeStr);
        $this->addTick($runTime, function () use ($taskName,$marketSource){
            $autoMarket = $this->cacheService->get( $this->cacheKey);
            if (null == $autoMarket || $autoMarket == 0) {
                app(ItickCommon::class)->getRun($taskName,$marketSource,$this->host,  $this->path,$this->cacheKey,$this->typeStr);
            }
        });
    }
}