<?php
/**
 * @Date: 2025/6/26 5:39
 */
declare(strict_types=1);
namespace app\sys\controller\agent;

use app\common\enum\agent\LevelEnum;
use app\common\services\ProductService;
use app\sys\controller\Base;
use app\sys\validate\AgentValidate;
use think\facade\App;
use app\common\services\agent\AgentService;

/**
 * 代理商
 * Class Agent
 * @package app\sys\controller\agent
 */
class Agent extends Base
{
    /**
     * Agent constructor.
     * @param App $app
     * @param AgentService $service
     */
    public function __construct(App $app, AgentService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 代理商列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['account_like', ''],
            ['time', []],
        ]);
        $agentIds = $this->getAgentChildIds();
        $this->success($this->service->getAgentList($data,$agentIds));
    }

    /**
     * 添加代理商
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['account', ''],
            ['real_name', ''],
            ['avatar',''],
            ['level',$this->service->dao->model->maxLevel],
            ['pid',0],
            ['ratio',0.00],
            ['balance',0.00000000],
            ['ratio_fee',0.00],
            ['pwd',''],
            ['conf_pwd',''],
            ['invite_code',''],
            ['status',1],
            ['kefu_qq',''],
            ['kefu_wechat',''],
        ]);
        validate(AgentValidate::class)->check($data);
        if($data['level'] < 1 || $data['level'] > 3){
            $this->error('添加代理失败[级别错误]');
        }
        if($data['pid'] > 0){
            $detail = $this->service->getDetail($data['pid']);
            $data['p_nickname'] = $detail['real_name'] ?? '';
        }
        if(empty($data['invite_code'])){
            $data['invite_code'] = $this->service->getInviteCode();
        }
        $data['roles'] = [$data['level']];

        if (!empty($data['pwd'] )) {
            # code...
            $data['pwd'] = $this->service->dao->passwordHash($data['pwd']);
        }
        

        $data['ratio_agent_fee'] = json_encode($this->service->getInitFee(null),JSON_UNESCAPED_UNICODE);
        if($this->service->create($data)){
            $this->success('添加代理成功!');
        }
        $this->error($this->service->getError()?:'添加代理失败');

    }

    /**
     * 修改代理商
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['account', ''],
            ['real_name', ''],
            ['avatar',''],
            ['level',$this->service->dao->model->maxLevel],
            ['pid',0],
            ['ratio',0.00],
            ['balance',0.00],
            ['ratio_fee',0.00],
            ['pwd',''],
            ['conf_pwd',''],
            ['invite_code',''],
            ['status',1],
            ['kefu_qq',''],
            ['kefu_wechat',''],
        ]);
        validate(AgentValidate::class)->check($data);

        if(empty($data['invite_code'])){
            $this->error('邀请码不能为空！');
        }
        if($data['level'] < 1 || $data['level'] > 3){
            $this->error('修改代理失败[级别错误]');
        }
        $detail = $this->service->getDetail($data['id']);
        if(empty($detail)){
            $this->error('该代理商不存在！');
        }
        if($data['pid'] > 0){
            $pDetail = $this->service->getDetail($data['pid']);
            $data['p_nickname'] = $pDetail['real_name'] ?? '';
        }

        if($data['invite_code'] != $detail['invite_code'] && $this->service->dao->model->where('id','<>',$data['id'])->where('invite_code','=',$data['invite_code'])->count() > 0){
            $this->error('该邀请码已存在！');
        }

        if( $this->service->updateSave($data['id'],$data)){
            $this->success('修改代理商成功!');
        }
        $this->error($this->service->getError()?:'修改代理商失败!');
    }

    /**
     * 修改代理商状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $detail = $this->service->getDetail($id);
        if(empty($detail)){
            $this->error('该代理商不存在！');
        }
        if ($this->service->update($id, ['status' => $this->request->param('status')])) {
            $this->success('修改成功');
        }
        $this->error('修改失败');
    }

    /**
     * 删除代理商
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $detail = $this->service->getDetail($id);
        if (!$detail) {
            $this->error('该账号不存在！');
        }

        if ($this->service->destroy((int)$id)) {
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }
    /**
     * 代理商详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(): void
    {
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 获取可用代理级别数据(根据当前登录账号)
     * @noAuth(true)
     * @method(GET)
     */
    public function getLevelData(){
        $levels = LevelEnum::data();
        $this->success('获取成功',$levels);
    }

    /**
     * 获取可用代理上级数据(根据当前登录账号)
     * @noAuth(true)
     * @method(GET)
     */
    public function getAgentSelect(){
        $level = $this->request->param('level');
        $agent = $this->service->getLevelAgentSelect($level);
        $this->success('获取成功',$agent);
    }

    /**
     * 获取可用代理上级数据(根据当前登录账号)
     * @noAuth(true)
     * @method(GET)
     */
    public function getAgentSelectByPid(){
        if (is_null($pid = $this->request->param('pid'))) {
            $this->error('参数错误!');
        }

        $agent = $this->service->getAgentSelectByPid((int)$pid);
        $this->success('获取成功',$agent);
    }

    /**
     * 获取代理手续费设置
     * @noAuth(true)
     * @method(GET)
     */
    public function getFee(){
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }

        $agent = $this->service->dao->model->where('id',$id)->find();
        if(empty($agent)){
            $this->error('代理不存在');
        }

        $proSelect = app(ProductService::class)->dao->model->order('sort desc,id asc')->column('id,name','id');

        $jishu_arr = [];
        $r_a_f_arr_1 = [];
        $r_a_f_arr_2 = [];
        $r_a_f_arr_3 = [];

        switch ($agent['level']['value']) {
            case 1:
                //一级代理
                $admin_user_1 = $agent;
                $r_a_f_arr_1 = $this->service->getInitFee($admin_user_1['ratio_agent_fee']);
                $r_a_f_arr = $r_a_f_arr_1;
                break;
            case 2:
                //二级代理
                $admin_user_2 = $agent;
                $r_a_f_arr_2 = $this->service->getInitFee($admin_user_2['ratio_agent_fee']);

                $admin_user_1 = $this->service->getDetail($admin_user_2['pid']);
                $r_a_f_arr_1 = $this->service->getInitFee($admin_user_1['ratio_agent_fee']);

                $r_a_f_arr = $r_a_f_arr_2;
                $jishu_arr = [$r_a_f_arr_1];
                break;
            case 3:
                //三级代理
                $admin_user_3 = $agent;
                $r_a_f_arr_3 = $this->service->getInitFee($admin_user_3['ratio_agent_fee']);

                $admin_user_2 = $this->service->getDetail($admin_user_3['pid']);
                $r_a_f_arr_2 = $this->service->getInitFee($admin_user_2['ratio_agent_fee']);

                $admin_user_1 = $this->service->getDetail($admin_user_2['pid']);
                $r_a_f_arr_1 = $this->service->getInitFee($admin_user_1['ratio_agent_fee']);

                $r_a_f_arr = $r_a_f_arr_3;
                $jishu_arr = [$r_a_f_arr_1,$r_a_f_arr_2];
                break;
            default:
                $r_a_f_arr = [];
                break;
        }
        $feeList = [];
        foreach ($proSelect as $key => $val){
            $jishu_num = 0;
            if ($jishu_arr){
                foreach ($jishu_arr as $key_j => $val_j){
                    if (!empty($val_j[$key])){
                        $jishu_num += $val_j[$key]['dist'] ?? 0;
                        $jishu_num += $val_j[$key]['markup'] ?? 0;
                    }
                }
            }

            if (!empty($r_a_f_arr[$key])){
                $r_a_f_arr[$key]["id"] = $val['id'];
                $r_a_f_arr[$key]["title"] = $val['name'];
                $r_a_f_arr[$key]["jishu"] = $jishu_num;
                $feeList[] = $r_a_f_arr[$key];
            }else{
                $feeList[] = [
                    'dist'=>0,
                    'markup'=>0,
                    "id"=>$val['id'],
                    "title"=>$val['name'],
                    "jishu"=>$jishu_num
                ];
            }
        }
        $this->success('获取成功',$feeList);
    }
    /**
     * 设置手续费
     * @method(POST)
     */

    public function setFee(){

        $data = $this->request->postMore([
            ['id', ''],
            ['fees', ''],
        ]);

        $fee = [];

        foreach ($data['fees'] as $key => $val){
            $fee[$val['id']] = [
                'id'=>$val['id'],
                'dist'=>$val['dist'],
                'markup'=>$val['markup'],
            ];
        }
        if($this->service->update([
            'id'=>$data['id'],
        ],[
            'ratio_agent_fee'=>json_encode($fee,JSON_UNESCAPED_UNICODE),
        ])){
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
    /**
     * 获取级联
     * @noAuth(true)
     * @method(GET)
     */
    public function getCascader(){
        [$list, $ids] = $this->service->getCascaderData();
        $this->success("获取成功",$list);
    }


}