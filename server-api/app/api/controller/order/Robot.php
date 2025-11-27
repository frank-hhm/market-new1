<?php
/**
 * @Date: 2025/7/1 19:25
 */
declare(strict_types=1);
namespace app\api\controller\order;
use app\common\constants\CacheKeyConstant;
use app\common\services\common\CacheService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderRobotService;
use think\facade\App;

/**
 * Class Order
 */
class Robot extends \app\api\controller\Base
{


    /**
     * Robot constructor.
     * @param App $app
     * @param OrderRobotService $service
     */
    public function __construct(App $app, OrderRobotService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    public function getOrderDetail(){
        $params = $this->request->getMore([
            ['id', ''],
        ]);
        $this->success('获取成功', $this->service->getDetail($params['id']));
    }


    /**
     * 委托
     * @method(POST)
     */
    public function createEntrust(){
        $params = $this->request->getMore([
            ['order_type', ''],
            ['order_price', ''],
            ['buy_price', ''],
            ['onumber', ''],
            ['surplus', ''],
            ['loss', ''],
            ['order_pid', ''],
        ]);
        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':createEntrust:'.$this->uid)){
            $this->error("请勿重复操作");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':createEntrust:'.$this->uid,1,1);


        //
        $params['surplus'] = "";
        $params['loss'] = "";
        $memberService = app(MemberOrderService::class);
        if ( $res = $memberService->createWeiTuo(
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
            $this->success('挂单成功！',$res);
        }else{
            $this->error($memberService->getError()?:'挂单失败，请重试！');
        }
    }

    /**
     * 撤单
     * @method(POST)
     */
    public function cancel(){

        $params = $this->request->getMore([
            ['id', ''],
        ]);
        $orderRobotService = app(OrderRobotService::class);
        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':cancelOrder:'.$this->uid.':'.$params['id'])){
            $this->error("请勿重复操作");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':cancelOrder:'.$this->uid.':'.$params['id'],1,1);
        $del = $orderRobotService->cancelOrder($this->uid,$params['id']);
        if($del){
            $this->success('撤单成功！');
        }
        $this->error('撤单成功');
    }

    /**
     * [addGuarantee APP修改止损]
     * @method(POST)
     */
    public function upYsWei()
    {
        $params = $this->request->getMore([
            ['surplus', ''],
            ['loss', ''],
            ['order_pid', ''],
            ['newprice', ''],
        ]);

        $memberService = app(MemberOrderService::class);
        if ( $res = $memberService->updateWeiTuo(
            $this->uid,
            $params['newprice'],
            $params['surplus'],
            $params['loss'],
            $params['order_pid']
        )){
            $this->success('修改成功！');
        }else{
            $this->error($memberService->getError()?:'修改失败，请重试！');
        }
    }


}