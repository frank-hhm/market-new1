<?php
/**
 * @Date: 2025/7/13 23:09
 */
declare(strict_types=1);
namespace app\sys\controller\activity;

use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\activity\TurntableRecordLogService;
/**
 * 转盘抽奖记录
 * Class TurntableRecordLog
 * @package app\sys\controller\member
 */
class TurntableRecordLog extends Base
{
    /**
     * TurntableRecordLog constructor.
     */
    public function __construct(App $app, TurntableRecordLogService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 记录列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['username_like',''],
            ['agent_id','all'],
            ['create_time',[]]
        ]);

        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data,$agentIds);
        $list['count_data'] = $this->service->getSum($data,$agentIds);
        $this->success('获取成功',$list);
    }
}