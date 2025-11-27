<?php
/**
 * @Date: 2025/7/6 11:25
 */
declare(strict_types=1);
namespace app\agentApi\controller\member;
use app\agentApi\controller\Base;
use app\common\services\member\MemberService;
use think\facade\App;

/**
 * 会员
 * Class Member
 * @package app\agentApi\controller\member
 */
class Member extends Base
{
    /**
     * Member constructor.
     */
    public function __construct(App $app, MemberService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 会员列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['card_like', ''],
            ['username_like', ''],
            ['agent_id', []],
            ['create_time', []]
        ]);
        $agentIds = $this->getAgentChildIds();
        $this->success('获取成功', $this->service->getMemberList($data, $agentIds));
    }
}