<?php
/**
 * @Date: 2025/7/6 13:29
 */
declare(strict_types=1);
namespace app\agentApi\controller\finance;

use app\agentApi\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\member\MemberCoinService;
/**
 * 钱包
 * Class Coin
 * @package app\agentApi\controller\finance
 */
class Coin extends Base
{
    /**
     * Water constructor.
     */
    public function __construct(App $app, MemberCoinService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 钱包列表
     * @method(GET)
     */
    public function list()
    {
        $params = $this->request->getMore([
            ['username_like', ''],
            ['moni', 'all'],
            ['agent_id', []],
            ['table_sorter', '']
        ]);
//        dump();die;
        $agentIds = $this->getAgentChildIds();
        $data = $this->service->getList($params, $agentIds);
        $data['balance'] = $this->service->getSum($params, $agentIds);
        $data['yingkui'] = $this->service->getSum($params, $agentIds, 'yingkui');
        $data['keyong'] = $this->service->getSum($params, $agentIds, 'keyong');
        $data['jingzhi'] = $this->service->getSum($params, $agentIds, 'jingzhi');
        $data['count'] = $data['balance'] + $data['yingkui'];

        $this->success('获取成功', $data);
    }
}