<?php

declare(strict_types=1);


namespace app\sys\controller\follow;

use app\sys\controller\Base;
use think\facade\App;
use app\common\services\follow\OrderService;
/**
 * 跟单订单
 * Class Order
 * @package app\sys\controller\follow
 */
class Order extends Base
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
     * 订单列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
            ["create_time",[]],
            ["username_like",""],
            ["person_like",""],
            ["status","all"],
            ['agent_id',[]],
        ]);
        $agentIds = $this->getAgentChildIds();
        $this->success("获取成功",$this->service->getList($data,$agentIds));
    }



    /**
     * 结束订单状态
     * @method(PUT)
     */
    public function endStatus()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        if ($this->service->onEndStatus($id)) {
            $this->success('处理成功');
        }
        $this->error('处理失败');
    }

}