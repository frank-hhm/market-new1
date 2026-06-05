<?php
/**
 * @Date: 2025/7/14 22:27
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\constants\CacheKeyConstant;
use app\common\dao\MarketKLineDao;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use think\facade\Db;
use think\facade\Queue;

/**
 * 产品K线数据
 * Class MarketKLineDataService
 */
class MarketKLineDataService
{
    public function createKData($price, $product, $nowTime, $dataTime = 0,$frameData = []): void
    {
        if ($dataTime === 0) {
            $dataTime = strtotime(date('Y-m-d H:i', $nowTime) . ':00');
        }
        //存储k线值
        $min = date('i');
        //1min
        $type = 1;
        $this->setKData($price, $product, $type, $dataTime,$frameData);


        //5min
        $hour = floor($min / 5) * 5;
        $minute = date('Y-m-d H:' . $hour, $nowTime) . ':00';
        $dataTime = strtotime($minute);
        $type = 5;
        $this->setKData($price, $product, $type, $dataTime,$frameData);

        //15min
        $hour = floor($min / 15) * 15;
        $minute = date('Y-m-d H:' . $hour, $nowTime) . ':00';
        $dataTime = strtotime($minute);
        $type = 15;
        $this->setKData($price, $product, $type, $dataTime,$frameData);

        //30min
        $hour = floor($min / 30) * 30;
        $minute = date('Y-m-d H:' . $hour, $nowTime) . ':00';
        $dataTime = strtotime($minute);
        $type = 30;
        $this->setKData($price, $product, $type, $dataTime,$frameData);

        //60min
        $minute = date('Y-m-d H', $nowTime) . ':00:00';
        $dataTime = strtotime($minute);
        $type = 60;
        $this->setKData($price, $product, $type, $dataTime,$frameData);

        //240min
        $hour = date('H');
        $hour = floor($hour / 4) * 4; // 计算当前时间所在的4小时间隔
        $minute = date('Y-m-d H', strtotime(date('Y-m-d ' . $hour . ':00:00', $nowTime))) . ':00:00'; // 构造时间字符串
        $dataTime = strtotime($minute); // 转换为时间戳
        $type = 240;
        $this->setKData($price, $product, $type, $dataTime,$frameData);

        // 日K线 (1-day)
        $minute = date('Y-m-d', $nowTime) . ' 00:00:00'; // 当天的零点时间
        $dataTime = strtotime($minute); // 转换为时间戳
        $type = 1440; // 1天 = 1440分钟
        $this->setKData($price, $product, $type, $dataTime,$frameData);
    }

    public function setKData($price, $product, $type, $dataTime,$frameData = [])
    {
            $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);

            $klineCacheKey = CacheKeyConstant::PRODUCT_K_LINE . ':' . $product['id'] . ':' . $type . ':' . $dataTime;
            $kLineMap['pid'] = $product['id'];
            $kLineMap['type'] = $type;
            $kLineMap['ktime'] = $dataTime;
            //产品小数点
            $productDecimal = $product['decimal'] ?? 2;

            $typeTime = [
                1 => 60,
                5 => 300,
                15 => 900,
                30 => 1800,
                60 => 3600,
                240 => 14400,
                1440 => 86400,
            ];
            $cacheTime = $typeTime[$type];

            $marketKLineService = app(MarketKLineService::class);

            if ($cacheService->has($klineCacheKey) && !empty($issetkline = $marketKLineService->dao->model->where('pid', $kLineMap['pid'])
                    ->where('type', $type)
                    ->where('ktime', $dataTime)
                    ->limit(1)
                    ->order('id', 'desc')->find())) {

                $updateKLineMap['id'] = $issetkline['id'];

                $updateKLineMap['volume'] = $product['volume'] ?? 0;
                $updateKLineMap['type'] = $issetkline['type'];
                $updateKLineMap['ktime'] = $issetkline['ktime'];
                $updateKLineMap['close_price'] = sprintf("%." . $productDecimal . "f", $price);

                if ($issetkline['height_price'] < $price) {
                    $updateKLineMap['height_price'] = sprintf("%." . $productDecimal . "f", $price);
                } else {
                    $updateKLineMap['height_price'] = sprintf("%." . $productDecimal . "f", $issetkline['height_price']);
                }
                if ($issetkline['low_price'] > $price) {
                    $updateKLineMap['low_price'] = sprintf("%." . $productDecimal . "f", $price);
                } else {
                    $updateKLineMap['low_price'] = sprintf("%." . $productDecimal . "f", $issetkline['low_price']);
                }

                $cacheService->set($klineCacheKey, $updateKLineMap, $cacheTime + 60 * 2);
//                $updateKLineMap["volume"] = Db::Raw("volume+" . rand(0,6) / 10);
                Db::name('market_k_line')->where('id', $updateKLineMap['id'])->update($updateKLineMap);

                if ($type === 1) {
                    app(ProductDataService::class)->dao->model->where('pid', $product['id'])->update([
                        'price' => sprintf("%." . $productDecimal . "f", $price),
                    ]);
                }

                //修改当天最高最低
                if ($type == 1440) {
                    $pro_data = array();
                    $pro_data['height_price'] = sprintf("%." . $productDecimal . "f", $issetkline['height_price']);
                    $pro_data['low_price'] = sprintf("%." . $productDecimal . "f", $issetkline['low_price']);
                    $pro_data['price'] = sprintf("%." . $productDecimal . "f", $price);
                    $pro_data['close_price'] = sprintf("%." . $productDecimal . "f", $price);
                    app(ProductDataService::class)->dao->model->where('pid', $product['id'])->update($pro_data);
                }
            } elseif (!$cacheService->has($klineCacheKey) && $marketKLineService->dao->model->where('pid', $kLineMap['pid'])
                ->where('type', $type)
                ->where('ktime', $dataTime)
                ->limit(1)->count() === 0) {
                $klineLockCacheKey = CacheKeyConstant::PRODUCT_K_LINE . 'lock:' . $product['id']. ':' . $type . ':' . $dataTime;
                // 尝试加锁（仅第一个请求能成功）
//                $acquired = $cacheService->setnx($klineLockCacheKey, 1);
//                if ($acquired) {
//                    // 加锁成功：允许执行一次数据库插入/更新逻辑
//                    try {
                        $cacheService->expire($klineLockCacheKey, 5);
                        $kLineMap['low_price'] = sprintf("%." . $productDecimal . "f", $price);
                        $kLineMap['height_price'] = sprintf("%." . $productDecimal . "f", $price);
                        $kLineMap['open_price'] = sprintf("%." . $productDecimal . "f", $price);
                        $kLineMap['close_price'] = sprintf("%." . $productDecimal . "f", $price);
                        $kLineMap['volume'] = $product['volume'] ?? 0;

                        $cacheService->set($klineCacheKey, $kLineMap, $cacheTime + 60 * 2);
                        $id = Db::name('market_k_line')->insertGetId(array_merge($kLineMap, ['create_time' => time()]));
                        $queueData = [
                            'pid' => $kLineMap['pid'],
                            'decimal' => $productDecimal,
                            'price' => sprintf("%." . $productDecimal . "f", $price),
                            'data' => $kLineMap,
                            'other' => $frameData
                        ];
                        $res = Queue::push("app\common\jobs\SetKDataJob", $queueData, 'SetKDataJob');
                        //app(ConsoleLogService::class)->create('SetKDataJob:'.$res,true);
                        if ($type === 1) {
                            app(ProductDataService::class)->dao->model->where('pid', $product['id'])->update([
                                'price' => sprintf("%." . $productDecimal . "f", $price),
                            ]);
                        } elseif ($type === 1440) {
                            app(ProductDataService::class)->dao->model->where('pid', $product['id'])->update([
                                'open_price' => sprintf("%." . $productDecimal . "f", $price),
                                'height_price' => sprintf("%." . $productDecimal . "f", $price),
                                'low_price' => sprintf("%." . $productDecimal . "f", $price),
                                'close_price' => sprintf("%." . $productDecimal . "f", $price),
                            ]);
                        }
//                    } finally {
//                        // 最后删除锁（可选，也可以等 expire 自动失效）
//                        $cacheService->del($klineLockCacheKey);
//                    }

//                } else {
//                    // 加锁失败：说明已经有其他请求在处理了，直接跳过
//                    echo "已被锁定，跳过处理。\n";
//                }
            }

            //价格更新队列
//            $queueData = [
//                'pid' => $kLineMap['pid'],
//                'price' => sprintf("%." . $productDecimal . "f", $price),
//            ];
//            $res = Queue::push("app\common\jobs\CheckOrderJob", $queueData, 'CheckOrderJob');

    }
}