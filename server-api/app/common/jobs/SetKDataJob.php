<?php
/**
 * @Date: 2025/6/27 4:55
 */
declare(strict_types=1);
namespace app\common\jobs;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\ConsoleLogService;
use app\common\services\MarketKLineService;
use app\common\services\ProductCacheService;
use app\common\services\ProductDataService;
use think\facade\Db;
use think\queue\Job;

/**
 * K线记录
 */
class SetKDataJob
{
    /**
     * fire是消息队列默认调用的方法
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
            return true;
        }

        if ($this->doJob($data)) {
            $job->delete();
        }
        return true;
    }

    public function doJob($data)
    {

        return $this->createKLineFail($data);
    }


    public function createKLineFail($data)
    {
        $pid = $data['pid'];
        $decimal = $data['decimal'];
        $kMapData = $data['data'];
        $price = $data['price'];
        $other = $data['other'] ?? [];
        return $this->setKData($pid, $decimal, $kMapData, $price,$other);
    }

    public function setKData($pid, $decimal, $kMapData, $price,$other = [])
    {
        $marketKLineService = app(MarketKLineService::class);
        $lastKMapKTime = $kMapData['ktime'] - ($kMapData['type'] * 60);
        $lastKLine = $marketKLineService->dao->model->where('pid', $pid)
            ->where('type', $kMapData['type'])
            ->where('ktime <= ' . $lastKMapKTime)
            ->limit(1)
            ->order('ktime', 'desc')->find();


        if ($lastKLine) {
            $lastKMap['close_price'] = sprintf("%." . $decimal . "f", $price);
            if ($lastKLine['height_price'] < $price) {
                $lastKMap['height_price'] = sprintf("%." . $decimal . "f", $price);
            }
            if ($lastKLine['low_price'] > $price) {
                $lastKMap['low_price'] = sprintf("%." . $decimal . "f", $price);
            }
            Db::name('market_k_line')->where('id', $lastKLine['id'])->update($lastKMap);
            if ($kMapData['type'] == 1440) {
                $productDataService = app(ProductDataService::class);
                $productDataService->dao->model->where('pid', $pid)->update([
                    'last_close' => sprintf("%." . $decimal . "f", $price),
                ]);
            }
        }
//        app(ConsoleLogService::class)->create(json_encode($other,JSON_UNESCAPED_UNICODE),true,"markets");
        return true;

    }
}