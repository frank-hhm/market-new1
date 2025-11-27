<?php
/**
 * @Date: 2025/6/30 15:42
 */
declare(strict_types=1);
namespace app\sys\controller\finance;

use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\finance\MemberWithdrawalService;
/**
 * 用户提现
 * Class MemberWithdrawal
 * @package app\sys\controller\member
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
            ['username_like',''],
            ['status',''],
            ['agent_id',[]],
            ['create_time',[]]
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data,$agentIds);
        $list['count_data'] = $this->service->getSum($data,$agentIds);
        $this->success('获取成功',$list);
    }

    /**
     * 用户提现审核
     * @method(PUT)
     */
    public function handle()
    {
        $data = $this->request->getMore([
            ['id',0],
            ['status',0],
        ]);
        if (empty($data['id'])) {
            $this->error('参数错误!');
        }
        if ($this->service->handle($data)) {
            $this->success('处理成功');
        }
        $this->error('处理失败');
    }
}