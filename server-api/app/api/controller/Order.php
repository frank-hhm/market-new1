<?php
/**
 * @Date: 2025/6/27 15:39
 */
declare(strict_types=1);
namespace app\api\controller;
use app\api\services\OrderService;
use app\common\constants\CacheKeyConstant;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderRobotService;
use think\facade\App;

/**
 * Class Order
 */
class Order extends \app\api\controller\Base
{


    /**
     * Order constructor.
     * @param App $app
     * @param OrderService $service
     */
    public function __construct(App $app, OrderService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 获取详细
     * @method(GET)
     */
    public function getOrderDetail()
    {
        $params = $this->request->getMore([
            ['id', ''],
        ]);
        $this->success('获取成功', $this->service->getDetailApi($params['id']));
    }

    /**
     * 下单
     * @method(POST)
     */
    public function create(){
        $params = $this->request->getMore([
            ['order_type',1],
            ['order_price', ''],
            ['buy_price', ''],
            ['onumber', ''],
            ['order_pid', ''],
            ['surplus', ''],
            ['loss', ''],
        ]);

        if (!StringHelper::isValidDecimal($params['onumber'])){
            $this->error('下单手数错误！手数为两位小数或整数！');
        }

        $memberOrderService = app(MemberOrderService::class);

        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':createOrder:'.$this->uid)){
            $this->error("请勿重复操作");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':createOrder:'.$this->uid,1,1);

        if($res = $memberOrderService->createOrder(
            $this->uid,
            $params['order_type'],
            $params['order_price'],
            $params['buy_price'],
            $params['onumber'],
            $params['surplus'],
            $params['loss'],
            $params['order_pid']
        )){
            isset($res['create_time']) && $res['create_time_text'] = date('Y-m-d H:i:s',$res['create_time']);
            isset($res['buytime']) && $res['buytime_text'] = date('Y-m-d H:i:s',$res['buytime']);

            $this->success('ok',$res);
        }
        $this->error($memberOrderService->getError()?:'开仓失败');
    }

    /**
     * 平仓
     * @method(POST)
     */
    public function sell()
    {
        $params = $this->request->getMore([
            ['oid', ''],
        ]);
        $oid = $params['oid'];

        $memberOrderService =  app(MemberOrderService::class);

        if($res = $memberOrderService->pingCang($this->uid,$oid,SellTypeEnum::USER)){
            $this->success('平仓成功！',$res);
        }else{
            $this->error($memberOrderService->getError()?:'平仓失败，请重试！',[],$memberOrderService->getErrorCode()?:0);
        }
    }

    /*
     * 获取当天平仓记录
     */
    public function getTodayComplateOrderSelect(){
        $listData = $this->service->dao->model->where('member_id',$this->uid)
            ->where('ostaus',1)
            ->where('selltime','>',strtotime(date('Y-m-d')))
            ->order('selltime','desc')
            ->select()->toArray();
            $this->success('获取成功！',$listData);
    }

    /**
     * [addGuarantee APP修改止损]
     * @method(POST)
     */
    public function upYs()
    {
        $params = $this->request->getMore([
            ['surplus', ''],
            ['loss', ''],
            ['oid', ''],
            ['mark_price', ''],
            ['trigger_price', '']
        ]);

        $memberOrderService =  app(MemberOrderService::class);

        if($res = $memberOrderService->upYsOrder(
            $this->uid,
            $params['trigger_price'],
            $params['mark_price'],
            $params['surplus'],
            $params['loss'],
            $params['oid']
        )){
            $this->success('委托修改处理成功');
        }
        $this->error($memberOrderService->getError()?:'委托修改处理失败');
    }

    /**
     * 获取历史盈亏
     * @method(POST)
     */
    public function chengList()
    {
        $rel = [];

        $params = $this->request->getMore([
            ['start_date', ''],
            ['end_date', '']
        ]);

        $params['ostaus'] = 1;
        $list = $this->service->getOrderList($this->uid,$params);
        $onumber_sum = 0;
        $ploss_sum = 0;
        foreach ($list as $key => $val){
            $onumber_sum += $val['onumber'];
            $ploss_sum += $val['ploss'];
        }

        $res['onumber_sum'] = $onumber_sum;
        $res['ploss_sum'] = $ploss_sum;
        $res['list'] = $list;
        sleep(1);
        $this->success("获取成功",$res);
    }


}