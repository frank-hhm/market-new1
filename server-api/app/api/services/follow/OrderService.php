<?php
declare(strict_types=1);
namespace app\api\services\follow;

use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\WaterService;
use app\common\services\follow\OrderService as CommonOrderService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use think\facade\Db;

/**
 *
 * Class OrderService
 */
class OrderService extends CommonOrderService
{


    public function getFollowOrder($memberId = 0,$personId = 0){
        return $this->dao->model->where([
                [
                    "member_id","=",$memberId
                ],[
                    "person_id","=",$personId
                ],[
                    "status","=",1
                ],
            ])->limit(1)->find();
    }


    public function isFollowStatus($memberId = 0,$personId = 0){
        return $this->dao->model->where([
                [
                    "member_id","=",$memberId
                ],[
                    "person_id","=",$personId
                ],[
                    "status","=",1
                ],
            ])->limit(1)->count() > 0;
    }

    public function getMemberCountByPerson($personId){
        return $this->dao->model->where([
                [
                    "person_id","=",$personId
                ]
            ])->limit(1)->count();
    }

    public function getToDayRevenue($memberId){
        $select = $this->dao->model->with(["person"])->where([
            [
                "member_id","=",$memberId
            ],[
                "status","=",1
            ],[
                "create_time","<",strtotime('yesterday 18:00:00'),
            ]
        ])->select()->toArray();
        $revenueAll = [];
        foreach ($select as $item){
            if (empty($item["person"])) continue;
            if ($item["person"]["revenue_type"] == "auto"){
                $rand = ($item["person"]["revenue_max"] + $item["person"]["revenue_min"]) / 2;
            }else{
                $rand = $item["person"]["revenue_lock"];
            }
            $revenueAll[] = sprintf("%.2f", ($item["money"] * $rand / 100));
        }
        return array_sum($revenueAll);
    }

    public function getMemberFollowOrderBalance($memberId = 0){
        return $this->dao->model->where([
            ["member_id","=",$memberId],
            ["status","=",1]
        ])->sum("money");
    }

    public function createOrder($memberId,$data){
        $isFollowStatus = $this->isFollowStatus($memberId,$data["id"]);
//        if ($this->isFollowStatus($memberId,$data["id"])){
//            $this->error = "跟单失败，已存在跟单!";
//            return false;
//        }
        $memberService = app(\app\api\services\member\MemberService::class);
        $member = $memberService->dao->model->where('id', $memberId)->find();

        $memberCoin = app(MemberCoinService::class)->dao->model->where('member_id',$memberId)->find();
        $memberBalance = $memberCoin['balance'];
        $money = $data["money"] ?? 0.00;
        Db::startTrans();
        if ($isFollowStatus){
//            $this->error = "跟单失败，已存在跟单!";
//            return false;
            $res[] =  $ids = $this->dao->model->where([
                [
                    "member_id","=",$memberId
                ],[
                    "person_id","=",$data["id"]
                ],[
                    "status","=",1
                ],
            ])->update([
                "update_time"=>time(),
                "money"=>Db::raw('money+'.$money)
            ]);
        }else{

            if($money < 1000 ){
                $this->error = '投入跟单最低1000u起跟！';
                Db::rollback();
                return false;
            }
            $saveData["member_id"] = $memberId;
            $saveData["person_id"] = $data["id"];
            $saveData["money"] = $money;
            $saveData["status"] = 1;
            $saveData["create_time"] = time();
            $res[] =  $ids = $this->dao->model->insertGetId( $saveData);
        }
        $res[] = $memberCoin->save(['balance'=>Db::raw('balance-'.$money)]);
        $res[] = app(MemberService::class)->deleteCacheDetail($memberId);
        $res[] = app(WaterService::class)->dao->model->insert([
            'member_id' => $memberId,
            'agent_id' => $member['agent_id'],
            'money' => $money,
            'remark' => '',
            'source' => SourceEnum::FOLLOW,
            'source_id' => $ids,
            'pay_type' => WithdrawalTypeEnum::BALANCE,
            'member_balance' => $memberBalance,
            'balance' => ($memberBalance - $money),
            'describe' => $isFollowStatus?'跟单继续投入':'跟单',
            'type'=>0,
            'create_time' => time(),
            'update_time' => time(),
        ]);
        if (!empty($res) && ArrayHelper::checkArr($res)) {
            Db::commit();
            app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'createFollowOrder','member_id'=>$memberId]);
            return true;
        } else {
            Db::rollback();
            $this->error = "跟单失败，请重试!";
            return false;
        }
    }



    public function getMemberOrderListApi($memberId = 0 ,$params = []){
        [$page, $limit] = $this->dao->getPageValue();
        $map = [];
        if (isset($params["status"])){
            $map[] = ["status","=",$params["status"]];
        }
        $list = $this->dao->model->with([
            "person"
        ])->withCount(['personOrder' => function($query, &$alias) {
            $alias = 'member_count';
        }])->where([
            [
                "member_id","=",$memberId
            ]
        ])->where($map)->order([ "create_time"=>"DESC"])->page($page)->paginate($limit)->toArray();
        return $list;
    }
}