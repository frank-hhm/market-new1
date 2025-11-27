<?php
/**
 * @Date: 2025/7/6 11:25
 */
declare(strict_types=1);

namespace app\agentApi\controller;
use app\BaseController;
use app\common\services\agent\AgentService;

/**
 * 控制器基础类
 */
class Base extends BaseController
{

    /**
     * 当前登陆信息
     */
    protected mixed $agentInfo;

    /**
     * 当前登陆管理员ID
     */
    protected int $agentId;
    // 初始化
    protected function initialize(): void
    {
        if($this->request->isLogin()) {
            $this->agentId = $this->request->agentId() ?? 0;
            $this->agentInfo = $this->request->agent('info') ?? [];
        }
    }

    public function getAgentChildIds(){
        $level = 3;
        !empty($this->agentInfo['level']['value']) && $level = $this->agentInfo['level']['value'];
        $ids = [];
        switch ($level){
            case 1:
                $ids = app(AgentService::class)->dao->model->where('level',$level + 1)->where('pid',$this->agentId)->column('id');
                $idsChild= app(AgentService::class)->dao->model->where('level',$level + 2)->where('pid','in',$ids)->column('id');
                $ids = array_merge($ids,$idsChild);
                break;
            case 2:
                $ids = app(AgentService::class)->dao->model->where('level',$level + 1)->where('pid',$this->agentId)->column('id');
                break;
        }
        $ids[] = $this->agentId;
        return $ids;
    }
}
