<?php
/**
 * @Date: 2025/3/27 17:37
 */
declare(strict_types=1);
namespace app\common\tasks\market;

use app\common\constants\CacheKeyConstant;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use app\common\services\MarketKLineDataService;
use app\common\services\MarketKLineService;
use app\common\services\ProductCacheService;
use app\common\services\ProductDataService;
use app\common\services\ProductPriceService;
use app\websocket\services\PushMarketService;
use EasySwoole\Component\Process\AbstractProcess;

/**
 * 定时任务：自主行情生成器
 * Class MarketAutoTask
 * @package app\common\tasks\product
 */
class MarketAutoTask extends AbstractProcess
{
    public array $productSelect = [];
    public array $productMarkets = [];

    public mixed $cacheService = null;

    public mixed $logService = null;

    public mixed $marketService = null;

    public function run($arg)
    {
        $this->cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $this->logService = app(ConsoleLogService::class);
        // TODO: Implement run() method.
        $this->logService->create("【MarketAutoTask】程序开始",true);

        $this->initProduct();
        //启动之前先清理缓存价格
        $this->deleteAllCachePrice();

        $this->createMarketAutoTask();
        $this->addTick(1000, function () {
            $autoMarket = $this->cacheService->get(CacheKeyConstant::MARKET_AUTO_STATUS);
//            $this->logService->create("【MarketAutoTask】程序-当前状态：".$autoMarket,true);
            if (null == $autoMarket || $autoMarket == 0) {
                $this->logService->create("【MarketAutoTask】程序重启",true);
                $this->createMarketAutoTask();
            }
        });
    }


    public function setStatus($status): void
    {
        $this->cacheService->set(CacheKeyConstant::MARKET_AUTO_STATUS,$status);
    }

    public function createMarketAutoTask(): void
    {
        $this->marketService = app(MarketKLineDataService::class);
        go(function () {
            $time = time();
            //当前毫秒级
            $timeCheck = $time . "000";
            $this->setStatus(1);
            while (1) {
                $timeS = ProductHelper::msecTime();
                if ($timeS > $timeCheck) {
//                    $autoRand = rand(1, 3);
                    $autoRand = rand(1, 6);
//                    $autoRand = rand(1, 20);
                    if ($autoRand > 2) {
                        $this->getSelectPrice();
//                        var_dump($this->productMarkets);
                        if (empty($this->productMarkets)) {
                            break;
                        }
                        foreach ($this->productMarkets as $v){
                            if(isset($v['price'])){
                                $ktime = time();
                                $v['price'] = sprintf("%.".$v["decimal"]."f",$v['price']);
                                $v['ktime'] = date("Y-m-d H:i:s",$ktime);
//                                echo $v['ktime'] ."产品：".$v['id']."  价格：".$v['price']."\n";
                                $this->cacheService->set(CacheKeyConstant::PRODUCT_PRICE.":".$v['id'],$v['price'],60);
                                $v["volume"] = rand(0,6) * 100;
                                PushMarketService::instance()->pushMarket($v['id'],$v);
                                $this->marketService->createKData($v['price'],$v,$ktime);
//                                $this->logService->create($v['ktime'] ."产品：".$v['id']."  价格：".$v['price'],true);
                            }
                        }
                        //Coroutine::sleep(1);
                    }
                    $timeCheck += 500;
                }
            }
            $this->setStatus(0);
        });
    }


    public function initProduct(): void
    {
        //产品列表
        $resData = app(ProductCacheService::class)->getProductCacheSelect(true);
        if( !empty($resData)){
            $this->productSelect = $resData;
        }else{
            $this->logService->create("【MarketAutoTask】程序-初始化数据失败");
            $this->setStatus(0);
        }
    }

    public function deleteAllCachePrice(): void
    {
        foreach ($this->productSelect as $item){
            $cacheKey = CacheKeyConstant::PRODUCT_PRICE.":".$item['id'];
            $this->cacheService->delete($cacheKey);
        }
    }


    public function getSelectPrice(): void
    {
        $this->productMarkets = [];
        foreach ($this->productSelect as $item) {
            $productCache = app(ProductCacheService::class)->getProductDetailCache($item['id']);
//            var_dump($productCache);
            if (empty($productCache)) {
                continue;
            }
            //验证休市
            if ($productCache['market_source']['value'] !== 'auto') {
                continue;
            }
            $isOpen = ProductHelper::checkIsOpen($productCache['open_time']);
            if (!empty($productCache['open_time']) && !$isOpen) {
                continue;
            }

            if ($productCache['status']['value'] == 0) {
                continue;
            }
            $proItem['id'] = $productCache['id'];
//            $proItem['is_open'] = $productCache['is_open'];
            $proItem['real_code'] = $productCache['real_code'];
            $proItem['decimal'] = $productCache['decimal'];
                $proItem['price'] = $this->getItemPrice($productCache);
            $this->productMarkets[$productCache['id']] = $proItem;
        }
    }
    public function getItemPrice($proItem)
    {
        $cacheKey = CacheKeyConstant::PRODUCT_PRICE.":".$proItem['id'];
        $price = $this->cacheService->get($cacheKey);
        $tmpPrice = $proItem['price'];
        if(empty($price)){
            $price = $proItem['price'];
        }
        if(empty($price) || $price <= 0){
            $productData = app(ProductDataService::class)->dao->model->where("pid",$proItem['id'])->find();
            if (!empty($productData)){
                $price = $productData["price"] ?? $productData["close_price"]?? $productData["open_price"];
            }
        }
        try {
            $nowTime = time();
            //后台调控价位

            $cacheHouTag = CacheKeyConstant::PRODUCT_SET_PRICE_HOU;
            if(!empty($priceSetPin = $this->cacheService->get(CacheKeyConstant::PRODUCT_SET_PRICE_PIN.':'.$proItem['id']))){
                // 获取插针价格 马上清除缓存
                $this->cacheService->delete(CacheKeyConstant::PRODUCT_SET_PRICE_PIN.':'.$proItem['id']);
                $this->logService->create("【MarketAutoTask】程序-插针价格：". $priceSetPin,true);
                $price = $priceSetPin;
            }else if (!empty($priceSetDetail = $this->cacheService->get($cacheHouTag.":detail:".$proItem['id'])) || !empty($priceSetDetail = app(ProductPriceService::class)->dao->model
                    ->where([
                        ['pid', '=', $proItem['id']],
                        ['type', '=',1],
                        ['execute_time', '<=', $nowTime],
                        ['complete_time', '>=', $nowTime],
                        ['status', '=', 'failure']
                    ])
                    ->order('id', 'desc')->limit(1)->find()) && !empty($priceSetDetail['price'])) {

                $endTime = StringHelper::_strtotime($priceSetDetail['complete_time']);
                $startTime = StringHelper::_strtotime($priceSetDetail['execute_time']);
                $cacheTime = $endTime - $startTime;
                //缓存上一次变动价格批次
                if(!$this->cacheService->has($cacheHouTag.":detail:".$proItem['id'])){
                    $this->cacheService->set($cacheHouTag.":detail:".$proItem['id'], $priceSetDetail->toArray(),$cacheTime + 10);
                }
                $lastBatchCacheKey = $cacheHouTag.":last_batch:".$proItem['id'];
                $lastBatchPrice = $this->cacheService->get($lastBatchCacheKey);
                $batchNumCacheKey = $cacheHouTag.":batch_num:".$proItem['id'];
                $batchNum = $this->cacheService->get($batchNumCacheKey);
                //缓存是否已分配变动金额
                $isHouBatchKey = $cacheHouTag.":is_hou_batch:".$proItem['id'];
                $isHouBatch = $this->cacheService->get($isHouBatchKey);
                //缓存计算的数据
                $houDataCacheKey = $cacheHouTag.":data:".$proItem['id'];
                $houData = $this->cacheService->get($houDataCacheKey);
                $minuteNum = date('i',$nowTime - 20);
                //dump($houData,$isHouBatch);
                //当前未计算
                if(empty($isHouBatch) || empty($houData)){
//                echo "时间:{$priceSetDetail['execute_time']} -   {$priceSetDetail['complete_time']}\n";
                    //总变动金额
                    $updatePrice = StringHelper::numberN($priceSetDetail['price'] -  $price,$priceSetDetail['decimal'] + 2);
//                        echo "原金额:【{$pro['Price']}】 改到金额: 【{$priceSetDetail['Price']}】   总变动金额:【{$updatePrice}】\n";
                    //时间差
                    $timeDiff = $endTime - $startTime - 30;
//                        echo "时间差:{$timeDiff}\n";
                    //每秒变动金额
//                        dump($updatePricde);
                    $secondPrice =  StringHelper::numberN($updatePrice / $timeDiff,$proItem['decimal'] + 2);

//                        dump($secondPrice);
                    //获取每分钟变动金额
                    $minutePrice =  StringHelper::numberN($secondPrice * 30,$proItem['decimal'] + 2);
//                    echo "每分钟:$minutePrice   每秒变动金额：$secondPrice\n";
                    $houData = [
                        'time_diff'=>$timeDiff,
                        'second_price'=>$secondPrice,
                        'minute_price'=>$minutePrice,
                        "update_old_price"=>$price,
                        "update_new_price"=>$priceSetDetail['price'] ,
                        "update_now_price"=>$price,
                        "type"=>$updatePrice > 0 ?1:-1,
                        "minute"=>$minuteNum,
                    ];
                    $this->cacheService->set($isHouBatchKey,true,$cacheTime + 10);
                    $this->cacheService->set($houDataCacheKey,$houData,$cacheTime + 10);
                }else {
                    //已计算分配金额
//                   dump($houData);
                    empty($lastBatchPrice) && $lastBatchPrice = 0;
                    //当前批次
                    empty($batchNum) && $batchNum = 1;
                    // 大于2的概率为2/3
                    if (
                        //必须条件
                        (
                            ($houData['type'] === 1 && $houData['update_new_price'] > $price)
                            || ($houData['type'] === -1 && $price > $houData['update_new_price'])
                        )
                        && (empty($houData['minute']) || $minuteNum >= $houData['minute'] || rand(1, 40) > 2)
                        && rand(1, 40) > 2
                    ) {
                        // 确保不会超过当前阶梯的目标金额
                        if (($houData['type'] === 1 && $houData['minute_price'] * 2 > $lastBatchPrice) || ( $houData['type'] === -1 && $houData['minute_price'] * 2 < $lastBatchPrice)) {
                            // 更新当前金额
//                                $eid_price = 0;
//                                echo "变动前价格:{$pro['Price']}\n";
                            $secondPrice = $houData['second_price'];
                            $eid_price =  sprintf("%.".$proItem['decimal']."f", $secondPrice);
                            if ($houData['type'] === 1 && ($price + $eid_price  * 2) > $houData['update_new_price']) {
                                $price = $houData['update_new_price'];
                            } elseif ($houData['type'] === -1 && $price - $eid_price * 2 < $houData['update_new_price']){
                                $price = $houData['update_new_price'];
                            }else {
                                if(rand(1, 10) > 9){
                                    $price = $price - ($eid_price / 1.5);
                                    $lastBatchPrice = $lastBatchPrice - $eid_price;
                                }else{
                                    $price = $price + ($eid_price  * 1.5);
                                    $lastBatchPrice = $lastBatchPrice + ($eid_price  * 1.5);
                                }
                            }
//                          dump($eid_price);
                            $houData['update_now_price'] = StringHelper::numberN($price,$proItem['decimal']);
                            //更新价格
                            $price =  sprintf("%.".$proItem['decimal']."f", $price);
//                            echo "价格:$price 每秒变动价格:$eid_price   小数：{$proItem['decimal']}\n";
                        } else {
                            $batchNum++;
                            $lastBatchPrice = 0;
                            $houData['minute'] = (int)$minuteNum + 1;
                        }
//                        echo "当前批次:{$batchNum}\n";
                        $this->cacheService->set($batchNumCacheKey, $batchNum,$cacheTime + 10);
                        $this->cacheService->set($lastBatchCacheKey, $lastBatchPrice,$cacheTime + 10);
                        $this->cacheService->set($houDataCacheKey, $houData,$cacheTime + 10);
                    }elseif (
                        ($houData['type'] === 1 && $price >= $houData['update_new_price'] )
                        || ($houData['type'] === -1 && $price <= $houData['update_new_price'])){
//                        dump("删除缓存");
                        $this->cacheService->delete($cacheHouTag.":detail:".$proItem['id']);
                        $this->cacheService->delete($isHouBatchKey);
                        $this->cacheService->delete($batchNumCacheKey);
                        $this->cacheService->delete($lastBatchCacheKey);
                        $this->cacheService->delete($houDataCacheKey);
                        app(ProductPriceService::class)->dao->model
                            ->where([
                                ['pid', '=', $proItem['id']],
                                ['type', '=',1],
                                ['execute_time', '<=', $nowTime],
                                ['complete_time', '>=', $nowTime],
                                ['status', '=', 'failure']
                            ])
                            ->update([
                                'status' => 'success',
                            ]);
                    }
                }
            }
            $FloatLength = $proItem['decimal'] ?? 2;
            //是否存在指定盈利
            $jishu_rand = pow(10, $FloatLength);
            $point_low = $proItem['point_min'] * $jishu_rand;
            $point_top = $proItem['point_max'] * $jishu_rand;
            $multiplier = 1000; // 这里假设选取了三位小数，因此乘以1000
            $intTop = (int) ($point_top * $multiplier);
            $intLow = (int) ($point_low * $multiplier);
            // 将得到的随机整数转换回小数形式
            if ($intLow > $intTop) {
                $rand = mt_rand($intTop,$intLow) / $multiplier / $jishu_rand;
            } else {
                $rand = mt_rand($intLow, $intTop) / $multiplier / $jishu_rand;
            }
            $zhangdie_rand = rand(1, 100);

            //涨跌控制
            $d_tiaojian = (date("d") % 2) == 0 ? "0" : "1";
            $h_tiaojian = (date("H") % 2) == 0 ? "0" : "1";
            $i_tiaojian = ((int)round(date("i") * 1.8) % 2 == 0) ? "0" : "1";
            $s_tiaojian = ((int)round(date("s") * 1.9) % 2 == 0) ? "0" : "1";

            $pid_tiaojian = mt_rand(0,1);

            if ($d_tiaojian) {
                $eid_price = $rand * 1;
            } else {
                $eid_price = $rand * -1;
            }
            $beiShuRand = mt_rand(1, 10);
            //优化无风控情况下突然回落 无人下单情况下
            $tmpCha = $price - $tmpPrice;
            //回落值|数据源 对比 比 2倍风控值 大
            $tmpChaJueDuiZhi = ProductHelper::getAbs($tmpCha) * 2;

            if ($tmpChaJueDuiZhi > $rand ) {
                if ($tmpCha < 0) {
                    if ($beiShuRand > 3) {
                        $eid_price = "-" . $rand;
                    } else {
                        $eid_price = $rand;
                    }
                } else {
                    if ($beiShuRand > 3) {
                        $eid_price = $rand;
                    } else {
                        $eid_price = "-" . $rand;
                    }
                }
            }
            if ($s_tiaojian) {
                $eid_price = $eid_price * 1;
            } else {
                $eid_price = $eid_price * -1;
            }
            if ($pid_tiaojian) {
                $eid_price = $eid_price * 1;
            } else {
                $eid_price = $eid_price * -1;
            }
            if ($h_tiaojian) {
                $eid_price = $eid_price * 1;
            } else {
                $eid_price = $eid_price * -1;
            }
            if ($i_tiaojian) {
                $eid_price = $eid_price * 1;
            } else {
                $eid_price = $eid_price * -1;
            }
            if ($zhangdie_rand > 30) {
                $eid_price = $eid_price * 1;
            } else {
                $eid_price = $eid_price * -1;
            }

            $newPrice = $price + $eid_price;
            return sprintf("%." . $FloatLength . "f", $newPrice);
        } catch (\Exception $e) {
            $this->setStatus(0);
            dump($e);
            $this->logService->create("【MarketAutoTask】程序-计算价格失败". $e->getMessage(),true);
            return $price;
        }
    }
}