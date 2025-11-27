<?php
/**
 * @Date: 2025/7/6 13:20
 */
declare(strict_types=1);
namespace app\agentApi\controller\finance;

use app\agentApi\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\finance\MemberRechargeService;
/**
 * 充值申请
 * Class Recharge
 * @package app\agentApi\controller\member
 */
class Recharge extends Base
{
    /**
     * Recharge constructor.
     */
    public function __construct(App $app, MemberRechargeService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 充值申请列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['username_like', ''],
            ['account', ''],
            ['status', ''],
            ['agent_id', []],
            ['create_time', []],
            ['payment_id', 'all']
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data, $agentIds);
        $list['count_data'] = $this->service->getSum($data, $agentIds);
        $this->success('获取成功', $list);
    }
}