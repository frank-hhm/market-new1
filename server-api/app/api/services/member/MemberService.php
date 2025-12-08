<?php
/**
 * @Date: 2025/6/26 1:22
 */
declare(strict_types=1);
namespace app\api\services\member;

use app\api\services\follow\OrderService as FollowOrderService;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\QrcodeHelper;
use app\common\services\agent\AgentService;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService as CommonMemberService;
use app\common\services\ProductCollectService;
use think\facade\Db;

/**
 *
 * Class MemberService
 */
class MemberService extends CommonMemberService
{

    public function getApiDetail($filter)
    {
        $detail = $this->dao->detail($filter,['coin']);
        if (!$detail ) {
            throw new CommonException('用户不存在');
        }
        $member = $detail->toArray();
        $qrcode = app(QrcodeHelper::class);
        $url = baseUrl().'/h5/#/pages/login/register';
        $params = [
            'mid'=>$member["id"],
            'code'=>$member['invite_code']
        ];
        $member['invite_url'] = baseUrl().$qrcode->getImage($url,$params);
        $member['invite_url2'] = $url.'?'.http_build_query($params);

//        }

        $member['people_moni'] = $this->getPeopleMoniStart($member["id"]);
        $member['people_start'] = $this->getPeopleStart($member["id"]);

        $tradeOrders = $this->getMemberTradeOrder($member["id"]);

        $collects = app(ProductCollectService::class)->getCollectIdsByMemberId($member["id"]);
        if (count($collects) == 0) {
            $collects = sysconf('member_default_product|json');
        }
        $member['collects'] = $collects;

        $FollowOrderService = app(FollowOrderService::class);
        $member["follow_revenue"] = $FollowOrderService->getToDayRevenue($member["id"]);
        $member["follow_revenue"] = sprintf("%.2f", $member["follow_revenue"] );
        $followBalance = sprintf("%.2f", $FollowOrderService->getMemberFollowOrderBalance($member["id"]) ?? 0);
        $member["follow_balance"] = $followBalance;
        //
        $agent = app(AgentService::class)->dao->model->where("id",$member["agent_id"])->find();
        $member["kefu_qq"] = $agent["kefu_qq"] ?? sysconf('kefu_qq');
        $member["kefu_wechat"] = $agent["kefu_wechat"] ?? sysconf('kefu_wechat');
        return  array_merge([
            'member' => $member,
        ],$tradeOrders);
    }
// 后台登陆获取菜单获取token
    public function login(string $username, string $password, string $type): array
    {
        $memberInfo = $this->verifyLogin($username, $password);
        return $this->loginMember($memberInfo,$type);
    }

    public function loginMember($memberInfo,string $type = 'member'): array
    {
        $jwtService = app(JwtAuthService::class);
        $tokenInfo = $jwtService->createToken($memberInfo->id, $type);
        $exp =  $tokenInfo['params']['exp'];
        return array_merge([
            'token' => $tokenInfo['token'],
            'expires_time' => $exp
        ], $this->getApiDetail([
            "id"=>$memberInfo->id
        ]));
    }

    // 账号登陆
    public function verifyLogin(string $username, string $password)
    {
        $memberInfo = $this->dao->model->where([
            'moni'=>0
        ])->where(function ($query) use ($username){
            $query->whereOr([
                [
                    ['username','=',$username]
                ],
                [
                    ['mobile','=',$username]
                ],
                [
                    ['email','=',$username]
                ]
            ]);
        })->find();
        if (!$memberInfo) {
            throw new CommonException('账号不存在!');
        }
        if (!$memberInfo['status']['value']) {
            throw new CommonException('您已被禁止登录!');
        }
        if (!password_verify($password, $memberInfo->password)) {
            throw new CommonException('账号或密码错误，请重新输入',701);
        }
        $memberInfo->last_time = time();
        $memberInfo->last_ip = app('request')->ip();
        $memberInfo->login_count++;
        $memberInfo->save();
        return $memberInfo;
    }

    public function getMemberByInviteCode($code){
        $detail = $this->dao->model->where(['invite_code' => $code])->find();
        return $detail ?? null;
    }
    public function register($data = []){
        $map = [];
        if ($data["register_type"] == "phone"){
            $map[] = ['mobile','=', $data['mobile']];
        }elseif ($data["register_type"] == "email"){
            $map[] = ['email','=', $data['email']];
        }else{
            throw new CommonException('注册来源不存在');
        }

        if($this->dao->model->where($map)->limit(1)->count() > 0){
            throw new CommonException('手机号/邮箱已存在');
        }
        $memberAgent = $this->getMemberByInviteCode($data['invite_code']);
        if(empty($memberAgent)){
            $agent = app(AgentService::class)->getAgentByInviteCode($data['invite_code']);
            if (!$agent){
                throw new CommonException('邀请码不存在');
            }
            $member['agent_id'] = $agent;
            $data['agent_type'] = 'agent';
        }else{
            $member['agent_id'] = $memberAgent['agent_id'];
            $data['agent_type'] = 'member';
            $member['people_id'] = $memberAgent['id'];
            //新增推广人数
        }
        $member['mobile'] = $data['mobile'];
        $member['register_type'] = $data['register_type'] ?? "phone";
        $member['email'] = $data['email'];
//        $member['username'] = $data['username'];
        $member['password'] = $this->dao->passwordHash($data['pwd']);
        $member['invite_code'] = $this->getInviteCode();
        $member['username'] = $member['invite_code'];

        Db::startTrans();
        try {
            if($resData = $this->dao->model->create($member)){
                $id = $resData->id;
                $res[] = $resData;
                if( $data['agent_type'] == 'member' ){
                    $res[] = $this->dao->model->where(['id' => $memberAgent['id']])
                        ->inc('people')
                        ->inc('people_tui')
                        ->update();
                }
                $res[] = app(MemberCoinService::class)->create([
                    'member_id'=> $id,
                    'agent_id'=> $member['agent_id'] ?? 0
                ]);


                #新增自选至默认
                $memberDefaultProduct = sysconf('member_default_product|json');
                if(!empty($memberDefaultProduct)){
                    $collects = [];
                    foreach ($memberDefaultProduct as $productId){
                        $collects[] = [
                            'member_id'=> $id,
                            'pid'=> $productId,
                            'create_time'=> time(),
                            'update_time'=> time(),
                        ];
                    }
                    $res[] = app(ProductCollectService::class)->dao->model->insertAll($collects);
                }

//                $redis = app(CacheService::class)->redisHandler();
//                #BTC
//                $redis->sAdd("userOptional:{$id}",39);
//                #黄金
//                $redis->sAdd("userOptional:{$id}", 35);
//                #LTPY
//                $redis->sAdd("userOptional:{$id}", 47);
//                # 恒指
//                $redis->sAdd("userOptional:{$id}", 27);
//                #原油
//                $redis->sAdd("userOptional:{$id}", 38);
//                #伦敦金
//                $redis->sAdd("userOptional:{$id}", 49);
            }
            if(ArrayHelper::checkArr($res)){
                Db::commit();
                return true;
            }else{
                Db::rollback();
                return false;
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }


    public function getPeopleMoniStart($uid){
        $peopleIds = $this->dao->model->where(['moni'=>0,'people_id'=>$uid])->where('moni_id','<>',0)->column('moni_id');
        $people = app(MemberCoinService::class)->dao->model->where('member_id','in',$peopleIds)->where('balance','<>',50000)->count();
        return $people;
    }

    public function getPeopleStart($uid){
        $count = $this->dao->model->where(['moni'=>0,'people_id'=>$uid])->where('bind_status',1)->count();
        return $count;
    }
}