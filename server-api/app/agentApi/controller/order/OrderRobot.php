<?php
/**
 * @Date: 2025/7/6 13:35
 */
declare(strict_types=1);
namespace app\agentApi\controller\order;

use app\agentApi\controller\Base;
use think\facade\App;
use app\common\services\order\OrderRobotService;

/**
 * 挂单记录
 * Class OrderRobot
 * @package app\agentApi\controller\order
 */
class OrderRobot extends Base
{
    /**
     * OrderRobot constructor.
     * @param App $app
     * @param OrderRobotService $service
     */
    public function __construct(App $app, OrderRobotService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 挂单列表
     * @method(GET)
     */
    public function guaList()
    {
        $data = $this->request->getMore([
            ['agent_id', []],
            ['moni', 'all'],
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getOrderRobotList(0, $data, $agentIds);
        $this->success('获取成功', $list);
    }
}