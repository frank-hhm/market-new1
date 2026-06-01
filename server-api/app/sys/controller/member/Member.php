<?php
/**
 * @Date: 2025/6/26 6:03
 */
declare(strict_types=1);
namespace app\sys\controller\member;
use app\common\services\member\MemberAgentService;
use app\common\services\member\MemberCoinService;
use app\common\services\ProductCollectService;
use app\common\services\ProductService;
use app\sys\controller\Base;
use think\facade\{App, Config, Db, Filesystem};
use app\common\services\member\MemberService;
/**
 * 会员
 * Class Member
 * @package app\sys\controller\member
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
            ['username_like',''],
            ['agent_id',[]],
            ['create_time',[]],
            ['table_sorter',''],
        ]);
        $agentIds = $this->getAgentChildIds();
        $this->success('获取成功',$this->service->getMemberList($data,$agentIds));
    }
    /**
     * 会员详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(): void
    {
        $data = $this->request->getMore([
            ['id', 0],
        ]);
        $this->success('获取成功',$this->service->getMemberDetail([
            'id'=> $data['id']
        ]));
    }
    /**
     * 添加会员
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['username', ''],
            ['mobile', ''],
            ['avatar',''],
            ['pwd',''],
            ['conf_pwd',''],
            ['status',1],
            ['fee_cash',0],
            ['agent_id',0],
            ['order_pin_time',0]
        ]);
        if(empty($data['username'])){
            $this->error('用户不能为空');
        }
        if(empty($data['mobile'])){
            $this->error('手机号不能为空');
        }
        $detail = $this->service->dao->model->whereOr([
            [
                ['mobile','=',$data['mobile']]
            ],
            [
                ['username','=',$data['username']]
            ]
        ])->limit(1)->count();
        if($detail){
            $this->error('该手机或用户名已存在！');
        }
        $data['invite_code'] = $this->service->getInviteCode();
        $data['password'] = $this->service->dao->passwordHash($data['pwd']);
        Db::startTrans();
        if($res = $this->service->create($data)){
            $id = $res->id;
            app(MemberCoinService::class)->create([
                'member_id'=> $id
            ]);

            #新增自选至默认
            $memberDefaultProduct = sysconf('member_default_product|json');
            if($res && !empty($memberDefaultProduct)){
                $collects = [];
                foreach ($memberDefaultProduct as $productId){
                    $collects[] = [
                        'member_id'=> $id,
                        'pid'=> $productId,
                        'create_time'=> time(),
                        'update_time'=> time(),
                    ];
                }
                $res = app(ProductCollectService::class)->dao->model->insertAll($collects);
            }
        }

        if($res){
            Db::commit();
            $this->success('添加会员成功!');
        }
        Db::rollback();
        $this->error('添加会员失败!');
    }

    /**
     * 修改会员
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['mobile', ''],
            ['avatar',''],
            ['pwd',''],
            ['conf_pwd',''],
            ['status',1],
            ['fee_cash',0],
            ['order_pin_time',0],
            ['agent_id',0],
            ['spread_cash',0]
        ]);
        if(empty($data['mobile'])){
            $this->error('手机号不能为空');
        }
        $detail = $this->service->dao->model->where([
            ['mobile','=',$data['mobile']],
            ['id','<>',$data['id']]
        ])->limit(1)->count();
        if($detail){
            $this->error('该手机已存在！');
        }
        if (!empty($data['pwd'])){
            if ($data["pwd"] !== $data["conf_pwd"]){
                $this->error('两次密码不一致!');
            }
            $data['password'] = $this->service->dao->passwordHash($data['pwd']);
        }
        if( $this->service->update($data['id'],$data)){
            app(MemberService::class)->deleteCacheDetail( $data['id']);
            $this->success('修改会员成功!');
        }
        $this->error('修改会员失败!');
    }


    /**
     * 修改会员绑定信息
     * @noAuth(true)
     * @method(PUT)
     */
    public function updateBind()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['bank_code', ''],
            ['bank_name',''],
            ['bank_real_name',''],
            ['usdt_card',''],
            ['alipay_name',''],
            ['alipay_card',''],
            ['alipay_img',''],
            ['withdraw_prompt',0],
            ['withdraw_prompt_text',''],

        ]);
        if( $this->service->update($data['id'],$data)){
            app(MemberService::class)->deleteCacheDetail( $data['id']);
            $this->success('修改会员绑定信息成功!');
        }
        $this->error('修改会员绑定信息失败!');
    }
    /**
     * 修改会员状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $detail = $this->service->getDetail($id);
        if(empty($detail)){
            $this->error('该会员不存在！');
        }
        if ($this->service->update($id, ['status' => $this->request->param('status')])) {
            app(MemberService::class)->deleteCacheDetail($id);
            $this->success('修改成功');
        }
        $this->error('修改失败');
    }

    /**
     * 删除会员
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $detail = $this->service->getDetail($id);
        if (!$detail) {
            $this->error('该会员不存在！');
        }
        if ($this->service->destroy((int)$id)) {
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }

    /**
     * 获取滑点配置
     * @method(GET)
     */
    public function getSlippage()
    {
        $data = $this->request->postMore([
            ['id', ''],
        ]);
        $proSelect = app(ProductService::class)->dao->model->order('sort desc,id asc')->column('id,name','id');
        $detail = $this->service->getDetail($data['id']);
        if(!empty($detail['slippage'])){
            $memberSlippage = json_decode($detail['slippage'],true);
        }else{
            $memberSlippage = [];
        }
        $feeList = [];
        foreach ($proSelect as $key => $val){
            $buy = 0;
            $sell = 0;
            if(isset($memberSlippage[$val['id']])){
                $buy = $memberSlippage[$val['id']]['buy'] ?? 0;
                $sell = $memberSlippage[$val['id']]['sell'] ?? 0;
            }else{
                foreach ($memberSlippage as $k => $v){
                    if($v['id'] == $val['id']){
                        $buy = $v['buy'] ?? 0;
                        $sell = $v['sell'] ?? 0;
                    }
                }
            }
            $feeList[] = [
                "id"=>$val['id'],
                "title"=>$val['name'],
                "buy"=>$buy,
                "sell"=>$sell
            ];
        }
        $this->success('获取成功',$feeList);
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

    public function setSlippage(){
        $data = $this->request->postMore([
            ['id', ''],
            ['slippage', ''],
        ]);

        $fee = [];

        foreach ($data['slippage'] as $key => $val){
            $fee[$val['id']] = [
                'id'=>$val['id'],
                'buy'=>$val['buy'] ?? 0,
                'sell'=>$val['sell'] ?? 0,
            ];
        }
        if($this->service->update([
            'id'=>$data['id'],
        ],[
            'slippage'=>json_encode($fee,JSON_UNESCAPED_UNICODE),
        ])){
            app(MemberService::class)->deleteCacheDetail( $data['id']);
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
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

    /**
     * 转移代理
     * @method(POST)
     */
    public function updateAgent()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['agent_id', ''],
        ]);
        if(empty($data['agent_id'])){
            $this->error('请选择代理');
        }
        $memberAgentService = app(MemberAgentService::class);
        if($memberAgentService->updateAgent($data['id'],$data['agent_id'])){
            $this->success('转移成功!');
        }
        $this->error($memberAgentService->getError()?:'转移失败!');
    }


    /**
     * 获取测试账号
     * @noAuth(true)
     * @method(GET)
     */
    public function getTestSelect(): void
    {
        $this->success('获取成功',$this->service->getMemberTestSelect());
    }
}