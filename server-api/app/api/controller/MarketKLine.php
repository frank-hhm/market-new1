<?php
/**
 * @Date: 2025/6/26 19:14
 */
declare(strict_types=1);
namespace app\api\controller;
use app\api\services\ProductService;
use app\common\services\common\CacheService;
use app\common\services\ProductCacheService;
use app\common\services\ProductDataService;
use think\facade\App;
use app\common\services\MarketKLineService;

/**
 * Class MarketKLine
 */
class MarketKLine extends \app\api\controller\Base
{
    /**
     * MarketKLine constructor.
     * @param App $app
     * @param MarketKLineService $service
     */
    public function __construct(App $app, MarketKLineService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * k线
     * @force(false)
     * @method(GET)
     */
    public function kLine(){
        $params = $this->request->getMore([
            ['num', 10],
            ['pid', 0],
            ['interval',1]
        ]);


        if(empty($params['pid'])){
            $this->error('参数错误');
        }
        if($params['num'] == 1000) {
            $params['num'] = 500;
        }

        $kline = $this->service->getKLineData((int)$params['pid'],$params['interval'],(int)$params['num']);

        $data['items'] = $kline;
        $this->success('获取成功',$data);
    }
}