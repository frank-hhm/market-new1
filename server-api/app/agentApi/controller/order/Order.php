<?php
/**
 * @Date: 2025/7/6 13:35
 */
declare(strict_types=1);
namespace app\agentApi\controller\order;

use app\agentApi\controller\Base;
use think\facade\App;
use app\common\services\order\OrderService;
use think\facade\Log;

/**
 * 订单
 * Class Order
 * @package app\sys\controller
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
     * 期货平仓列表
     * @method(GET)
     */
    public function pinList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle', ''],
            ['selltime', []],
            ['buytime', []],
            ['agent_id', []],
            ['is_wei', 'all']
        ]);
        $data['type'] = 1;
        $agentIds = $this->getAgentChildIds();
        $res = $this->service->getPinList($data, $agentIds);
        if (isset($res['money_profit'])) {
            $res['money_profit'] = sprintf("%.2f", $res['money_profit']);
        }
        if (isset($res['money_sx_fee'])) {
            $res['money_sx_fee'] = sprintf("%.2f", $res['money_sx_fee']);
        }
        if (isset($res['onumber'])) {
            $res['onumber'] = sprintf("%.2f", $res['onumber']);
        }
        $this->success('获取成功', $res);
    }

    /**
     * 期货持仓列表
     * @method(GET)
     */
    public function chiList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle', ''],
            ['buytime', []],
            ['agent_id', []],
            ['is_wei', 'all']
        ]);
        $data['type'] = 1;
        $agentIds = $this->getAgentChildIds();
        $res = $this->service->getChiList($data, $agentIds);
        if (isset($res['onumber'])) {
            $res['onumber'] = sprintf("%.2f", $res['onumber']);
        }
        $this->success('获取成功', $res);
    }

    /**
     * 模拟平仓列表
     * @method(GET)
     */
    public function pinMoniList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle', ''],
            ['selltime', []],
            ['buytime', []],
            ['agent_id', []],
            ['is_wei', 'all']
        ]);

        $agentIds = $this->getAgentChildIds();
        $agentIds[] = 0;
        $data['type'] = 1;

        $res = $this->service->getPinList($data, $agentIds, 1);
        if (isset($res['money_profit'])) {
            $res['money_profit'] = sprintf("%.2f", $res['money_profit']);
        }
        if (isset($res['money_sx_fee'])) {
            $res['money_sx_fee'] = sprintf("%.2f", $res['money_sx_fee']);
        }
        if (isset($res['onumber'])) {
            $res['onumber'] = sprintf("%.2f", $res['onumber']);
        }
        $this->success('获取成功', $res);
    }

    /**
     * 模拟持仓列表
     * @method(GET)
     */
    public function chiMoniList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle', ''],
            ['buytime', []],
            ['agent_id', []],
            ['is_wei', 'all']
        ]);

        $agentIds = $this->getAgentChildIds();
        $agentIds[] = 0;
        $data['type'] = 1;


        $res = $this->service->getChiList($data, $agentIds, 1);
        if (isset($res['onumber'])) {
            $res['onumber'] = sprintf("%.2f", $res['onumber']);
        }
        $this->success('获取成功', $res);
    }

}