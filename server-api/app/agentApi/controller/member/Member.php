<?php
/**
 * @Date: 2025/7/6 11:25
 */
declare(strict_types=1);
namespace app\agentApi\controller\member;
use app\agentApi\controller\Base;
use app\common\services\member\MemberService;
use app\common\services\ProductService;
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


    /**
     * 获取风控滑点配置
     * @method(GET)
     */
    public function getRisk()
    {
        $data = $this->request->postMore([
            ['id', ''],
        ]);
        $proSelect = app(ProductService::class)->dao->model->order('sort desc,id asc')->column('id,name','id');
        $detail = $this->service->getDetail($data['id']);
        if(!empty($detail['risk'])){
            $memberRisk = json_decode($detail['risk'],true);
        }else{
            $memberRisk = [];
        }
        $feeList = [];
        foreach ($proSelect as $key => $val){
            $status = 0;
            $triggerTime = 0;
            $monitorTime = 0;

            $price = 0;
            if(isset($memberRisk[$val['id']])){
                $price = $memberRisk[$val['id']]['price'] ?? 0;
                $status = $memberRisk[$val['id']]['status'] ?? 0;
                $triggerTime = $memberRisk[$val['id']]['trigger_time'] ?? 0;
                $monitorTime = $memberRisk[$val['id']]['monitor_time'] ?? 0;
            }else{
                foreach ($memberRisk as $k => $v){
                    if($v['id'] == $val['id']){
                        $price = $v['price'] ?? 0;
                        $status = $v['status'] ?? 0;
                        $triggerTime = $v['trigger_time'] ?? 0;
                        $monitorTime = $v['monitor_time'] ?? 0;
                    }
                }
            }
            $feeList[] = [
                "id"=>$val['id'],
                "title"=>$val['name'],
                "price"=>$price,
                "status"=>$status,
                "trigger_time"=>$triggerTime,
                "monitor_time"=>$monitorTime,
            ];
        }
        $this->success('获取成功',$feeList);
    }

    /**
     * 保存滑点配置
     * @method(POST)
     */

    public function setRisk(){
        $data = $this->request->postMore([
            ['id', ''],
            ['risk', ''],
        ]);

        $fee = [];

        foreach ($data['risk'] as $key => $val){
            $fee[$val['id']] = [
                'id'=>$val['id'],
                'monitor_time'=>$val['monitor_time'] ?? 0,
                'price'=>$val['price'] ?? 0,
                'status'=>$val['status'] ?? 0,
                'trigger_time'=>$val['trigger_time'] ?? 0,
            ];
        }
        if($this->service->update([
            'id'=>$data['id'],
        ],[
            'risk'=>json_encode($fee,JSON_UNESCAPED_UNICODE),
        ])){
            app(MemberService::class)->deleteCacheDetail( $data['id']);
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
}