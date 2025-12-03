<?php
/**
 * @Date: 2025/7/4 16:37
 */
declare(strict_types=1);
namespace app\common\tasks\market;

use app\common\constants\CacheKeyConstant;
use app\common\helper\ProductHelper;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use app\common\services\MarketKLineDataService;
use app\common\services\MarketKLineService;
use app\common\services\ProductCacheService;
use app\websocket\services\PushMarketService;
use EasySwoole\Component\Timer;
use Swoole\Coroutine\Http\Client;


class ItickCommon
{


    public string $cacheKey = '';

    public mixed $cacheService = null;

    public mixed $logService = null;

    //是否运行检测心跳
    public bool $checkStatus = false;

    public mixed $client = null;
    public array $productMarkets = [];

    public string $taskName = '';
    public array $productSelect = [];

    public string $productSubscribe = "";
    public string $typeStr = "";

    public mixed $marketService = null;
    public mixed $messageService = null;

    public mixed $timerId = null;
    public mixed $messageTime = [];

    public function getKey()
    {
        return sysconf('itick_key');
//        return "7952b98579fd478d9e49b6b3de82f4c99f17e889d5eb4fa6bdbba0238fc22861";
    }

    public function getRun($taskName,$marketSource,$host,$path,$cacheKey = '',$typeStr = '')
    {
        if(!empty($cacheKey)){
            $this->cacheKey = $cacheKey;
        }
        if(!empty($taskName)){
            $this->taskName = $taskName;
        }
        if(!empty($typeStr)){
            $this->typeStr = $typeStr;
        }
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        $this->marketService = app(MarketKLineDataService::class);
        $this->messageService = app(PushMarketService::class);
        $this->initProduct();
        go(function () use ($marketSource, $host, $path) {
            $this->createLog("开始连接".$this->taskName."服务器");
            $client = new Client($host,443,true);
            // 👇 注册协程退出时的清理函数（关键！）
            \Swoole\Coroutine::defer(function () use ($client) {
                if ($client && method_exists($client, 'isConnected') && $client->isConnected()) {
                    $client->close();
                    $this->createLog($this->taskName."【清理】协程退出，已关闭客户端连接");
                } else {
                    $this->createLog($this->taskName."【清理】客户端已断开或无需关闭");
                }
                if($this->timerId){
                    \Swoole\Timer::clear($this->timerId);
                }
            });
            try {
                $ret = $client->upgrade($path);
                if ($ret) {
                    // 持续监听推送
                    while (true) {
                        $frame = $client->recv();
                        if ($frame === false || !$frame->data) {
//                            dump($frame);
//                            dump($ret);
//                            dump($client, $this->getKey());
                            $this->createLog($this->taskName."服务器连接失败 ");
                            $this->setStatus(0);
                            break;
//                            Coroutine::sleep(1);
                        }else{
                            $frameData = json_decode($frame->data,true);
                            if( isset($frameData['code'])  && $frameData['code'] === 1){
                                $this->setStatus(1);
                                if(isset($frameData['msg']) && $frameData['msg'] == 'Connected Successfully'){
                                    // 步骤2：身份验证
//                                    echo "连接成功\n";
                                    $this->createLog($this->taskName."服务器连接成功 ");
                                    $this->setStatus(0);
                                    $auth = json_encode([
                                        "ac" => "auth",
                                        "params" => $this->getKey()
                                    ]);
                                    $client->push($auth);
                                }elseif(isset($frameData['msg']) && isset($frameData['resAc']) && $frameData['msg'] == 'authenticated' && $frameData['resAc'] === 'auth'){
//                                    echo "授权成功\n";
                                    $this->createLog($this->taskName."授权成功 ");
                                    // 步骤3：订阅请求
                                    $this->getMarket($marketSource);
                                    $this->createLog($this->taskName."提交订阅：".$this->productSubscribe);
                                    $subscribe = json_encode([
                                        "ac" => "subscribe",
                                        "params" => $this->productSubscribe,
                                        "types" => "quote",
                                    ]);
//                                    dump($marketString);
                                    $client->push($subscribe);
                                }elseif(isset($frameData['msg']) && isset($frameData['resAc']) && $frameData['msg'] == 'subscribe Successfully' && $frameData['resAc'] === 'subscribe'){
//                                    echo "订阅成功\n";
                                    // 订阅后间隔60秒发送一次心跳信息
                                    $this->createLog($this->taskName."订阅成功");
                                    $this->addHeartbeat($client,$marketSource);
                                    $this->setStatus(1);
                                }else if(isset($frameData['data']) && isset($frameData['data']['type']) && $frameData['data']['type'] === 'quote'){

//                                   dump($frameData);
                                    $proCode = $frameData['data']['s'];
                                    $proPrice = $frameData['data']['ld'];
                                    $High = $frameData['data']['h'];
                                    $Low = $frameData['data']['l'];
                                    $Open = $frameData['data']['o'];
                                    $LastClose = $frameData['data']['c'] ?? $proPrice;
                                    $proVol = (int)$frameData['data']['v'];
                                    $kTime = $frameData['data']['t'];
                                    $kTime = (int)( $kTime / 1000);
//                                    dump($frameData['data'],);
                                    $arr = array_column($this->productMarkets, null, 'real_code');
                                    if (isset($arr[$proCode]['id'])) {
                                        $proPrice = sprintf("%.".$arr[$proCode]["decimal"]."f",$proPrice);
                                        $arr[$proCode]['price'] = $proPrice;
                                        $arr[$proCode]["volume"] = $proVol;

                                        if( $this->cacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$arr[$proCode]['id']) !== $proPrice){
                                            $this->messageService->pushMarket($arr[$proCode]['id'],$arr[$proCode],$frameData['data']['t'] ?? $kTime);

                                            $this->cacheService->set(CacheKeyConstant::PRODUCT_PRICE.":".$arr[$proCode]['id'],$proPrice,60);
                                            $this->marketService->createKData($proPrice,$arr[$proCode],$kTime);
                                        }
                                    }
                                }
                            }else if( isset($frameData['resAc']) && $frameData['resAc'] === 'pong'){
                                $this->getMarket($marketSource);
                                $this->createLog($this->taskName."ping 成功");
                            }else{
                                $this->createLog($this->taskName."互通失败 ".(json_encode($frame->data,JSON_UNESCAPED_UNICODE) ?? ''));
                                $this->setStatus(0);
                                break;
                            }
                        }
                    }
                }else{
                    $this->createLog($this->taskName."连接失败");
                    $this->setStatus(0);
                }
                $client->close();

            } catch (\Throwable  $e) {
                $this->setStatus(0);
                $this->createLog($this->taskName."连接服务器失败：{$e->getMessage()}");
                $client->close();
            } finally {
                if ($client->connected) {
                    $client->close();
                    $this->createLog($this->taskName."连接已关闭：{$e->getMessage()}");
                }
            }
        });
    }



    public function createLog($msg){
        $this->logService->create($msg,true);
//        echo  $msg."\n";
    }



    public function setStatus($status){
        if($status == 0){
            $this->checkStatus = false;
            !empty($this->client) && $this->client->close();
        }
        $this->cacheService->set($this->cacheKey,$status);
    }

    // 添加心跳检测定时器的方法
    protected function addHeartbeat(Client $client,$marketSource)
    {
        if(!$this->checkStatus){
            $this->timerId = Timer::getInstance()->loop(1000 * 30, function () use ($client,$marketSource) {
                $time = time();
                $pingData = json_encode([
                    "ac" => "ping",
                    "params" => $time * 1000,
                ]);
                $client->push($pingData);
//                dump($pingData);
                $this->logService->create($this->taskName."心跳检测");
            });
            $this->checkStatus = true;
        }
    }



    public function initProduct(): void
    {
        //产品列表
        $resData = app(ProductCacheService::class)->getProductCacheSelect(true);
        if( !empty($resData)){
            $this->productSelect = $resData;
        }else{
            $this->createLog("【MarketAutoTask】程序-初始化数据失败");
            $this->setStatus(0);
        }
    }

    public function getMarket($marketSource)
    {
        $this->productMarkets = [];
        $this->productSubscribe = "";
        foreach ($this->productSelect as $item) {
            $productCache = app(ProductCacheService::class)->getProductDetailCache($item['id']);
            if (empty($productCache)) {
                continue;
            }
            if ($productCache['market_source']['value'] !== $marketSource) {
                continue;
            }

            $proItem['id'] = $productCache['id'];
            $proItem['real_code'] = $productCache['real_code'];
            $proItem['decimal'] = $productCache['decimal'];

            if ($item['real_code']) {
                if (empty($this->productSubscribe)){
                    $this->productSubscribe = $item['real_code'] .'$'.  $this->typeStr;
                }else{
                    $this->productSubscribe .= ",". $item['real_code'] . '$'. $this->typeStr;
                }
//                $this->productMarkets[$item['real_code']] = $proItem;
            }
            if ($productCache['status']['value'] == 0) {
                continue;
            }
            //验证休市
            $isOpen = ProductHelper::checkIsOpen($productCache['open_time']);
            if (!empty($productCache['open_time']) && !$isOpen) {
                continue;
            }
            $this->productMarkets[$item['real_code']] = $proItem;
        }
        return $this->productSubscribe;
    }
}