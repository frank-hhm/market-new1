<?php
/**
 * @Date: 2025/6/26 8:04
 */
declare(strict_types=1);
namespace app\api\controller\member;
use app\api\services\member\MemberCoinService;
use app\api\validate\MemberUpdatePassValidate;
use app\common\constants\CacheKeyConstant;
use app\common\enum\EnumFactory;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\StatusEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\helper\MailerHelper;
use app\common\helper\QrcodeHelper;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\MemberRechargeService;
use app\common\services\finance\MemberWithdrawalService;
use app\common\services\finance\WaterService;
use app\common\services\member\MemberCardService;
use app\common\services\member\MemberSubscribeService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderRobotService;
use app\common\services\pay\PayOrderService;
use app\common\services\ProductCollectService;
use think\facade\App;
use app\api\services\member\MemberService;
use think\facade\Config;
use think\facade\Env;
use app\api\services\follow\OrderService as FollowOrderService;

/**
 * Class Member
 */
class Member extends \app\api\controller\Base
{
    /**
     * MemberService constructor.
     * @param App $app
     * @param MemberService $service
     */
    public function __construct(App $app, MemberService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 获取用户详细
     * @method(GET)
     */
    public function detail()
    {
        $member = $this->service->getApiDetail(["id"=>$this->uid]);
        $this->success('获取成功', $member);
    }
    /*
     * 获取会员抽奖次数
     * @method(GET)
     */
    public function getMemberTurnTableCount(){
        $this->success('获取成功', [
            'turntable' => $this->member['turntable'] ?? 0,
        ]);
    }

    /**
     * 修改用户信息
     * @method(PUT)
     */
    public function update(){
        $data = [];
        if(!empty($nickName = $this->request->param('nickname'))){
            $data['nickname'] = $nickName;
        }
        if($this->service->update($this->uid, $data)){
            $this->service->deleteCacheDetail($this->uid);
            $this->success('保存成功!',$this->service->getDetail([
                'id'=>$this->uid
            ]));
        }
        $this->error('保存失败!');
    }

    /**
     * 实名
     * @method(POST)
     */
    public function bindReal(){

        $data = $this->vali([
            'real_name.require' => '姓名不能为空!',
            'card_number.require' => '身份证不能为空!',
            'card_number.idCard' => '身份证号错误!',
        ]);

        $member = $this->service->dao->model->where(['id' => $this->uid])->find();
        if (empty($member)){
            $this->error('该账号不存在');
        }
        if ($member['bind_status']['value'] == 1){
            $this->error("您已经实名过了");
        }else if ($member['bind_status']['value'] == 2){
            $this->error("您的实名信息审核中");
        }
        $data['agent_id'] = $member['agent_id'] ?? 0;
        if(app(MemberCardService::class)->createSave($this->uid,$data)){
            $this->success('申请成功，等待审核');
        }
        $this->success($this->service->getError()?:'申请失败');
    }


    /**
     * 充值
     * @method(POST)
     */
    public function recharge(){
        $data = $this->request->param();
        $this->checkBind();
        $cacheService = app(CacheService::class);
        if($cacheService->has(CacheKeyConstant::API_SUBMIT_LOCK.':recharge:'.$this->uid)){
            $this->error("请勿重复操作");
        }
        $cacheService->set(CacheKeyConstant::API_SUBMIT_LOCK.':recharge:'.$this->uid,1,5);

        $memberRechargeService = app(MemberRechargeService::class);
        if ($memberRechargeService->getMemberRecharge($this->uid) >= 2){
            $this->error("您有待审核的充值申请，请等待审核！");
        }
        sleep(1);
        if($memberRechargeService->recharge($data,$this->uid)){
            $this->success('充值申请成功，等待审核');
        }
        $this->success($this->service->getError()?:'申请失败');
    }

    /**
     * 支付宝充值
     * @method(POST)
     */
    public function alipayh5(){
        $data = $this->request->param();
        $this->checkBind();
        if(empty($data['money']) || empty($data['usdt'])){
            $this->error("输入金额不对！");
        }
        if(sysconf('payment_alipays_status') == 0){
            $this->error("通道关闭，请选择其他支付方式！");
        }
        $payOrder = app(PayOrderService::class);
        if($res = $payOrder->execute(
            $data['money'],
            $data['usdt'],
            $data['pay_type'] ?? 'web',
            $this->uid
        )){

            if(!empty($data['pay_type']) && $data['pay_type'] == 'app'){
                $resData = $res;
            }else{
                $resData = $res;
            }
            $this->success('获取成功',$resData);
        }else{
            $this->error('获取失败');
        }
    }


    /**
     * 退出登陆
     * @noAuth(true)
     * @method(GET)
     */
    public function logout(): void
    {
        $token = trim(ltrim($this->request->header(Config::get('cookie.token_name')), 'Bearer'));
        $cacheKey = Env::get('redis.jwt').':member:'.md5($token);
        if(app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER)->delete($cacheKey)){
            $this->success('退出登陆成功');
        }else{
            $this->error('退出登陆失败');
        }
    }

    /**
     * usdt充值
     * @method(POST)
     */
    public function usdtpayTransferApi(){
        $data = $this->request->param();

        $this->checkBind();

        $memberRechargeService = app(MemberRechargeService::class);
        if ($memberRechargeService->getMemberRecharge($this->uid) >= 2){
            $this->error("您有待审核的充值申请，请等待审核！");
        }
        sleep(1);
        if($memberRechargeService->recharge($data,$this->uid)){
            $this->success('充值申请成功，等待审核');
        }
        $this->success($this->service->getError()?:'申请失败');
    }



    /**
     * 修改密码
     * @method(PUT)
     */
    public function updatePass(): void
    {
        $data = $this->request->postMore([
            ['old_pwd', ''],
            ['pwd', ''],
            ['conf_pwd', ''],
        ]);

        validate(MemberUpdatePassValidate::class)->check($data);

        if( $this->service->updatePass($this->uid,$data)){
            $this->success('修改密码成功!');
        }
        $this->error('修改密码失败!');
    }

    /**
     * 找回密码
     * @force(false)
     * @method(PUT)
     */
    public function resetPass(){

        $data = $this->request->postMore([
            ['mobile', ''],
            ['code', ''],
            ['pwd', ''],
            ['type', ''],
        ]);
        if(empty($data['pwd'])){
            $this->error('密码不能为空!');
        }
        $username = $data["mobile"];
        $detail = $this->service->dao->model->where(function ($query) use ($username){
            $query->whereOr([
                [
                    ['mobile','=',$username]
                ],
                [
                    ['email','=',$username]
                ]
            ]);
        })->find();
        if(empty($detail)){
            $this->error('账号不存在!');
        }

        if($data["type"] == "phone"){
            $this->checkCode($data['mobile'],$data['code']);
        }else{
            MailerHelper::checkCode($data['mobile'],$data['code']);
        }
        if( $this->service->updatePass($detail['id'],$data,false)){
            $this->success('修改密码成功!');
        }
        $this->error('修改密码失败!');
    }


    /**
     *
     *  切换账号
     * @method(GET)
     */
    public function loginqie(){
        if($this->member['moni'] == 0){
            $moniId = $this->member['moni_id'];
            if($moniId == 0){
                $moniId = $this->service->createMoni($this->uid,$this->member['username'],$this->member['agent_id'] ?? 0);
                sleep(3);
            }
            $this->success('切换成功',$this->service->loginMoni($moniId));
        }else{
            $member = $this->service->dao->model->where([
                ['status','=',1],
                ['moni_id','=',$this->member['id']]
            ])->find();
            if(empty($member)){
                $this->error('切换失败，真实账号不存在');
            }
            $this->success('切换成功',$this->service->loginMember($member));
        }
    }

    /**
     * 绑定银行卡
     * @method(PUT)
     */
    public function bindBankCard(){

        $data = $this->request->postMore([
            ['bank_child', ''],
            ['bank_code', ''],
            ['bank_name', ''],
            ['bank_real_name', ''],
        ]);
        if(!empty($this->member['bank_code'])){
            $this->error("若需要修改联系客服");
        }
        if( $this->service->update($this->uid,$data)){
            $this->service->deleteCacheDetail($this->uid);
            $this->success('绑定成功!');
        }
        $this->error('绑定失败!');
    }

    /**
     * 绑定Usdt地址
     * @method(PUT)
     */
    public function bindUsdtApi(){

        $data = $this->request->postMore([
            ['usdt_card', ''],
            ['usdt_bep_card', ''],
        ]);
        if(!empty($this->member['usdt_card'])){
            $this->error("若需要修改联系客服");
        }

        if( $this->service->update($this->uid,$data)){
            $this->service->deleteCacheDetail($this->uid);
            $this->success('绑定成功!');
        }
        $this->error('绑定失败!');
    }

    /**
     * 绑定支付宝账号
     * @method(PUT)
     */
    public function bindAlipayApi(){

        $data = $this->request->postMore([
            ['alipay_card', ''],
            ['alipay_name', ''],
            ['alipay_img', ''],
        ]);
        if(!empty($this->member['alipay_card'])){
            $this->error("若需要修改联系客服");
        }

        if( $this->service->update($this->uid,$data)){
            $this->service->deleteCacheDetail($this->uid);
            $this->success('绑定成功!');
        }
        $this->error('绑定失败!');
    }


    /**
     * 抽奖
     * @method(POST)
     *
     */
    public function turnTable(){
        $data = $this->request->postMore([
            ['count', 0],
        ]);
        if( $this->service->turnTable($this->uid,$data)){
            $this->service->deleteCacheDetail($this->uid);
            $this->success('成功',$this->service->getDetail($this->uid));
        }
        $this->error($this->service->getError()?:'失败');
    }
    /**
     * 获取团队数据
     * @method(GET)
     *
     */
    public function getTeamList(){
        $data = $this->request->postMore([
        ]);
        $this->success('获取成功',$this->service->getTeamListApi($this->uid));
    }
    /**
     * 获取佣金详情
     * @method(GET)
     *
     */
    public function getCommissionDetail(){
        $member = app(MemberService::class)->dao->model->with(['coin'])->where('id',$this->uid)->find();
        if (!$member){
            $this->error('会员不存在');
        }
        $member = $member->toArray();
        $settlementPrice = app(MemberCommissionWaterService::class)->dao->model->where('people_id',$this->uid)->where('status',0)->sum('money');
        $settlementPrice += app(MemberFeeCashWaterService::class)->dao->model->where('member_id',$this->uid)->where('settlement_status',0)->sum('money');
        $member['commission_settlement_price'] = $settlementPrice;
        $member['commission_withdrawal_complete'] = app(WaterService::class)->dao->model->where('member_id',$this->uid)
            ->where('pay_type',WithdrawalTypeEnum::COMMISSION)
            ->where('source',SourceEnum::MEMBER_COMMISSION_WITHDRAWAL)
//            ->where('status',1)
            ->sum('money');
        $member['commission_withdrawal'] = app(MemberWithdrawalService::class)->dao->model->where('member_id',$this->uid)
            ->where('status',0)
            ->where('type',WithdrawalTypeEnum::COMMISSION)
            ->sum('money');

        $isWithdrawal = app(MemberWithdrawalService::class)->checkCommissionTime();
        $member['withdrawal_tips'] = "";
        $member['fee_cash_tips'] = "佣金将于交易日次日18:00发放至账户。您可在交易日次日18:00查看您的佣金。具体返佣金额请以实际发放为准。";
        $this->success('获取成功',$member);
    }



    /**
     * 订阅消息
     * @method(POST)
     *
     */
    public function createSubscribe(){
        $data = $this->request->postMore([
            ["source_id",0],
            ["source","default"]
        ]);
        $memberSubscribeService = app(MemberSubscribeService::class);
        if($memberSubscribeService->isExists(array_merge([
            "member_id"=>$this->uid
        ],$data))){
            if($memberSubscribeService->deleteSubscribe($this->uid,$data)){
                $this->success('取消订阅成功');
            }
            $this->error($memberSubscribeService->getError()?:"取消订阅失败");
        }else{
            if($memberSubscribeService->createSubscribe($this->uid,$data)){
                $this->success('订阅成功');
            }
            $this->error($memberSubscribeService->getError()?:"订阅失败");
        }
    }
}