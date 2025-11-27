<?php
/**
 * @Date: 2025/6/27 18:46
 */
declare(strict_types=1);
namespace app\sys\controller\finance;

use app\common\constants\CacheKeyConstant;
use app\common\services\common\CacheService;
use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\finance\MemberRechargeService;
/**
 * 充值申请
 * Class Recharge
 * @package app\sys\controller\member
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
            ['username_like',''],
            ['account', ''],
            ['status',''],
            ['agent_id',[]],
            ['create_time',[]],
            ['payment_id','all']
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getList($data,$agentIds);
        $list['count_data'] = $this->service->getSum($data,$agentIds);
        $this->success('获取成功',$list);
    }
    /**
     * 充值申请审核
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
        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':handleRecharge:'.$this->uid)){
            $this->error("请勿重复操作!稍等一分再试试");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':handleRecharge:'.$this->uid,1,60);

        if ($this->service->handle($data)) {
            $this->success('处理成功');
        }
        $this->error($this->service->getError()?:'处理失败');
    }
}