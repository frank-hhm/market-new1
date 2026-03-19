<?php
/**
 * @Date: 2025/6/26 0:53
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\api\services\follow\OrderService as FollowOrderService;
use app\api\services\OrderService;
use app\common\constants\CacheKeyConstant;
use app\common\dao\member\MemberDao;

use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\QrcodeHelper;
use app\common\helper\StringHelper;
use app\common\services\activity\TurntableRecordLogService;
use app\common\services\BaseService;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\WaterService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderRobotService;
use app\common\services\ProductCollectService;
use think\facade\Db;

/**
 * 用户
 * Class MemberService
 */
class MemberService extends BaseService
{
    /**
     * MemberService constructor.
     * @param MemberDao $dao
     */
    public function __construct(MemberDao $dao)
    {
        $this->dao = $dao;
    }



    public function accountByToId(string $account)
    {
        return $this->dao->model->where('username|mobile' ,'=', $account)->where(['status' => 1])->value('id');
    }


    public function getInviteCode()
    {
        $code = StringHelper::randString(3, 'letter1');
        $code .= StringHelper::randString(5, 'number');
        if ($this->dao->model->where([
                'invite_code' => $code
            ])->limit(1)->count() > 0) {
            $code = $this->getInviteCode();
        }
        return $code;
    }


    public function getDetail($filter,$exception = true)
    {
        $detail = $this->dao->detail($filter,['coin','peopleDetail']);
        if (!$detail ) {
            if(!$exception){
                return [];
            }
            throw new CommonException('用户不存在');
        }
        return $detail->toArray();
    }
    public function getMemberDetail($filter,$exception = true)
    {
        $detail = $this->dao->model->with(['coin'])->where($filter)->find();
        if (!$detail ) {
            if(!$exception){
                return [];
            }
            throw new CommonException('用户不存在');
        }
        return $detail->toArray();
    }

    public function getCacheDetail($id,$isInit = false){
        $cacheKey = "member:detail:".$id;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::MEMBER_REDIS_DRIVER);
        if(empty($detail = $cacheService->get($cacheKey)) || $isInit){
            try {
                $detail = $this->dao->model->with(['coin'])->where('id',$id)->find();
                if(!empty($detail)){
                    $detail = $detail->toArray();
                    $cacheService->set($cacheKey,$detail,60);
                    return $detail;
                }
            }catch (\Exception $e){
                Db::connect()->close();
                sleep(1);
                Db::connect();
                return $this->getCacheDetail($id);
            }
        }
        return $detail;
    }
    public function deleteCacheDetail($id){
        $cacheKey = "member:detail:".$id;
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::MEMBER_REDIS_DRIVER);
        if(!$cacheService->has($cacheKey)){
            $detail = $this->getCacheDetail($id, true);
        }else{
            $detail = $cacheService->get($cacheKey);
        }
        app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'memberDetail','member_id'=>$id]);
        return $detail;
    }

    public function getMemberList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $filter[] = ['moni','=',0];
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
            $filter[] = ['agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = ['agent_id','in',$agentIds];
        }
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        if(!empty($param['username_like']) ){
            $filter[] = ['username|mobile|nickname|email', 'like','%'.$param['username_like'].'%' ];
        }
        if(!empty($param['card_like']) ){
            $filter[] = ['real_name|card_number', 'like','%'.$param['card_like'].'%' ];
        }
        $coinTable = app(MemberCoinService::class)->dao->model->getTable();
        $thisTableName = $this->dao->model->getTable();
        $sorter = $thisTableName.'.create_time DESC';
        if(!empty($param['table_sorter'])){
            $tableSorter = [];
            foreach (json_decode($param['table_sorter'],true) as $key=>$value){
                if(in_array($key,['balance','yingkui_total','commission_balance','commission_total'])){
                    $tableSorter = [
                        $coinTable.'.'.$key=>$value
                    ];
                }elseif ($key == 'last_time'){
                    $tableSorter = [
                        $thisTableName.'.last_time'=>$value
                    ];
                }
            }
            $sorter = $tableSorter;
        }
        $list = $this->dao->model
            ->join($coinTable,"$thisTableName.id=$coinTable.member_id")
            ->field($thisTableName.".*,$coinTable.balance,$coinTable.member_id,$coinTable.yingkui_total,$coinTable.commission_balance,$coinTable.commission_total")
            ->with(['agent','peopleDetail'])
            ->where($filter)->order($sorter)->page($page)->paginate($limit)->toArray();
        $memberOrderService = app(MemberOrderService::class);
        foreach ($list['data'] as &$item){
             $memberTrade = $memberOrderService->getMemberTradeInfo($item['id']);
             !empty($memberTrade['yingkui_sum']) && $item["yingkui"] = $memberTrade['yingkui_sum'];
             !empty($memberTrade["jingzhi"]) &&  $item["jingzhi"] = $memberTrade['jingzhi'];
            !empty($memberTrade["money_keyong"]) &&  $item["money_keyong"] = $memberTrade['money_keyong'];
        }
        return $list;
    }


    /**
     * 修改密码
     */
    public function updatePass($id,array $data,$isOld = true): bool
    {
        $detail = $this->getDetail($id);
        if ($isOld && !password_verify($data['old_pwd'], $detail['password'])) {
            throw new CommonException('原密码错误，请重新输入');
        }

        //修改密码
        $pwd = $this->dao->passwordHash($data['pwd']);
        if ($this->dao->update($id,['password'=>$pwd])) {

            return true;
        } else {
            return false;
        }
    }


    public function turnTable($uid,$data)
    {
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::MEMBER_REDIS_DRIVER);
        if(!empty($cacheService->get('turntable:'.$uid))){
            throw new CommonException('请勿频繁操作[抽奖间隔30秒]');
        }
        $cacheService->set('turntable:'.$uid,1,30*1);
        $member = $this->getDetail($uid);
        if($member['turntable'] < 1){
            throw new CommonException('暂无抽奖次数');
        }
        Db::startTrans();
        try {
            $memberCoin = app(MemberCoinService::class)->dao->model->where(['member_id' =>$uid])->find();
            #减少抽奖次数
            $res[] = app(MemberService::class)->dao->model->where(['id' => $uid])->save([
                'turntable'=>Db::raw('turntable-1')
            ]);

            #中奖入账
            $res[] = app(MemberCoinService::class)->dao->model->where(['member_id' => $uid])->save([
                'balance'=>Db::raw('balance+'.$data['count'])
            ]);
            $memberBalance = $memberCoin['balance'];
            $res[]  =  app(WaterService::class)->create([
                'member_id' => $uid,
                'agent_id' => $member['agent_id'],
                'money' => $data['count'],
                'remark' =>  '',
                'source' =>'activity',
                'source_id' =>0,
                'pay_type' => RechargePayTypeEnum::TURNTABLE,
                'member_balance' => $memberBalance,
                'balance' => $memberBalance + $data['count'],
                'describe' => '中奖奖励',
                'type'=>1
            ]);

            //记录
            $res[] = app(TurntableRecordLogService::class)->dao->model->create([
                'member_id' => $uid,
                'agent_id' => $member['agent_id'],
                'number' => $data['count'],
                'status'=>1,
                'create_time'=> time()
            ]);

            $res[] = app(MemberService::class)->deleteCacheDetail($uid);
            if(!ArrayHelper::checkArr($res)){
                Db::rollback();
                return false;
            }
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }


    /**
     * 创建账号模拟账号
     */
    public function createMoni($memberId = 0,$username = '',$agentId = 0){
        Db::startTrans();
        try {
            $res = $this->dao->model->create([
                'username'=>$username.'_moni',
                'moni'=>1,
                'agent_id'=>$agentId
            ]);
            $moniId = $res->id;
            if($res){
                $res = app(MemberCoinService::class)->dao->model->create([
                    'member_id'=>$moniId,
                    'balance'=>50000,
                    'agent_id'=>$agentId
                ]);
            }
            if($res){
                $res = $this->dao->model->update([
                    'id'=>$memberId,
                    'moni_id'=>$moniId,
                    'agent_id'=>$agentId
                ]);
            }
            if(!$res){
                Db::rollback();
                return false;
            }
            // 事务提交
            Db::commit();
            return $moniId;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }
    public function loginMoni($moniId){
        $moniDetail = $this->getDetail(['id'=>$moniId]);
        $memberDetail = $this->getDetail(['moni_id'=>$moniId]);
        if(empty($moniDetail['id'])){
            $moniId = $this->createMoni($memberDetail['id'],$memberDetail['username']);
            $moniDetail = $this->getDetail(['id'=>$moniId]);
//            throw new CommonException('模拟账号不存在');
        }
        $jwtService = app(JwtAuthService::class);
        $tokenInfo = $jwtService->createToken($moniId, 'member');

        $exp =  $tokenInfo['params']['exp'];
        return array_merge([
            'token' => $tokenInfo['token'],
            'expires_time' => $exp,
        ],$this->getApiDetail([
            "id"=>$moniDetail["id"]
        ]));
    }

    public function getTeamListApi($uid){
        [$page, $limit] = $this->dao->getPageValue();
        $list = $this->dao->model->with(['coin'])->where([
            'people_id'=>$uid,
            'moni'=>0
        ])->order(['people DESC'])->page($page)->paginate($limit)->toArray();
        $followOrderService = app(FollowOrderService::class);
        $waterService = app(WaterService::class);
        $orderService = app(OrderService::class);
        foreach ($list["data"] as &$item){
            //今日合约
            $item["today_order_total"] = $orderService->dao->model->where("member_id",$item["id"])->whereDay('create_time')->sum("fee");
            //
            $item["today_follow_order_total"] = sprintf("%.2f", $followOrderService->dao->model->where([
                ["member_id","=",$item["id"]]

            ])->whereDay('create_time')->sum("money"));
            //跟单佣金
            $item["follow_commission_total"] = $waterService->dao->model->where("member_id",$uid)->where([
                "other_id"=>$item["id"]
            ])->where([
                'source' => SourceEnum::FOLLOW_REVENUE_COMMISSION
            ])->sum("money");
            //合约佣金

            $item["commission_balance"]= $waterService->dao->model->where("member_id",$uid)->where([
                "other_id"=>$item["id"]
            ])->where([
                'source' => SourceEnum::COMMISSION_FEE
            ])->sum("money");
        }
        return $list;
    }

    public function getMemberSlippage($pid,$slippage = [],$isBuy = true){
        $numberKey = "buy";
        if(!$isBuy){
            $numberKey = "sell";
        }
        $number = 0;
        if(!is_array($slippage) && !empty($slippage)){
            $slippage = json_decode($slippage,true);
        }
        if(empty($slippage)){
            return $number;
        }
        foreach ($slippage as $k => $v){
            if(isset($v[$pid])){
                $number = $v[$pid][$numberKey] ?? 0;
            }elseif($v['id'] == $pid){
                $number = $v[$numberKey] ?? 0;
            }
        }
        return $number;
    }



    public function getMemberTradeOrder($memberId){
        $memberTrade = app(MemberOrderService::class)->getMemberTradeInfo($memberId);

        //获取持仓订单
        $holdList = app(MemberOrderService::class)->getChiCangOrdersByMemberId($memberId);

        //获取挂单
        $robotList = app(OrderRobotService::class)->getOrderRobotListByMemberId($memberId);

        return [
            'member_coin' => $memberTrade,
            "order_hold"=>$holdList,
            "order_robot"=>$robotList
        ];
    }


    public function getMemberTestSelect(){

        return $this->dao->model->where('agent_id' ,'in', [2])->where('moni' ,'=', 0)->where(['status' => 1])->select()->toArray();
    }
}