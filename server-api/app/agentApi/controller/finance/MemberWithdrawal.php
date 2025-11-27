<?php
/**
 * @Date: 2025/7/6 13:23
 */
declare(strict_types=1);
namespace app\agentApi\controller\finance;

use app\agentApi\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\finance\MemberWithdrawalService;
/**
 * 用户提现
 * Class MemberWithdrawal
 * @package app\agentApi\controller\member
 */
class MemberWithdrawal extends Base
{
    /**
     * MemberWithdrawal constructor.
     */
    public function __construct(App $app, MemberWithdrawalService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 提现申请列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['username_like', ''],
            ['status', ''],
            ['agent_id', []],
            ['create_time', []]
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data, $agentIds);
        $list['count_data'] = $this->service->getSum($data, $agentIds);
        $this->success('获取成功', $list);
    }

}