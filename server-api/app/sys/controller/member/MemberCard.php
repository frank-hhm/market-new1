<?php
/**
 * @Date: 2025/6/26 6:54
 */
declare(strict_types=1);
namespace app\sys\controller\member;

use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\member\MemberCardService;
/**
 * 会员认证
 * Class MemberCard
 * @package app\sys\controller\member
 */
class MemberCard extends Base
{
    /**
     * MemberCard constructor.
     */
    public function __construct(App $app, MemberCardService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 会员认证列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['card_like', ''],
            ['username_like',''],
            ['agent_id',[]],
            ['create_time',[]]
        ]);
        $agentIds = $this->getAgentChildIds();
        $this->success('获取成功',$this->service->getList($data,$agentIds));
    }

    /**
     * 会员认证详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(): void
    {
        $this->success('获取成功',$this->service->getDetail($this->request->get('id')));
    }

    /**
     * 修改会员认证
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['card_number', ''],
            ['real_name',''],
        ]);
        if( $this->service->updateCard($data['id'],$data)){
            $this->success('修改信息成功!');
        }
        $this->error($this->service->getError()?:'修改信息失败!');
    }

    /**
     * 会员认证审核
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
        $this->error($this->service->getError()?:'处理失败');
    }
}