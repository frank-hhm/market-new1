<?php
/**
 * @Date: 2025/6/30 15:59
 */
declare(strict_types=1);
namespace app\sys\controller\finance;

use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\finance\WaterService;
/**
 * 流水
 * Class Water
 * @package app\sys\controller\member
 */
class Water extends Base
{
    /**
     * Water constructor.
     */
    public function __construct(App $app, WaterService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 资金流水列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['username_like',''],
            ['desc', ''],
            ['source',''],
            ['pay_type',''],
            ['agent_id',[]],
            ['create_time',[]]
        ]);

        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data,$agentIds);
        $list['count_data'] = $this->service->getSum($data,$agentIds);
        $this->success('获取成功',$list);
    }

    /**
     * 代理商资金明细
     * @method(GET)
     */
    public function agentList()
    {
        $data = $this->request->getMore([
            ['username_like',''],
            ['desc', ''],
            ['pay_type',''],
            ['agent_id',[]],
            ['create_time',[]]
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getAgentList($data,$agentIds);
        $list['count_data'] = $this->service->getSum($data,$agentIds,'money','agent');
        $this->success('获取成功',$list);
    }
}