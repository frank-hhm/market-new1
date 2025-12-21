<?php

declare(strict_types=1);

namespace app\common\services\follow;

use app\common\dao\follow\OrderDao;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\PushWebSockQueueService;
use app\common\services\finance\WaterService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use think\facade\Db;
use think\facade\Filesystem;
/**
 * 跟单订单
 * Class OrderService
 */
class OrderService extends BaseService
{

    public mixed $memberService = null;
    public mixed $memberCoinService = null;
    public mixed $waterService = null;

    public int $time = 0;
    /**
     * OrderService constructor.
     * @param OrderDao $dao
     */
    public function __construct(OrderDao $dao)
    {
        $this->dao = $dao;
        $this->memberService = app(MemberService::class);
        $this->memberCoinService = app(MemberCoinService::class);
        $this->waterService = app(WaterService::class);
        $this->time = time();
    }


    public function checkOrder(){
        $select = $this->dao->model
            ->with(["person"])
            ->where('status',1)
            ->where([
                ['create_time','<',strtotime('yesterday 18:00:00')],
            ])
            ->select()->toArray();
        $successCount = 0;
        $errorCount = 0;
        foreach ($select as $item){
            if ($this->waterService->dao->model->where([
                    ["member_id","=",$item["member_id"]],
                    ["source_id","=",$item["id"]],
                    ["source","=", SourceEnum::FOLLOW_REVENUE],
                    ["create_time",">=", StringHelper::_strtotime(date("Y-m-d", $this->time)." 00:00:00")],
                    ["create_time","<", $this->time],
                ])->limit(1)->count() > 0){
                continue;
            }
            if($this->settlement($item)){
                $successCount++;
            }else{
                $errorCount++;
            }
        }
        return ['success'=>$successCount,'error'=>$errorCount];
    }

    public function settlement($item){

        if ($item["person"]){
            if ($item["person"]["revenue_type"] == "auto"){
                if(!empty($item["person"]["revenue_min"]) && !empty($item["person"]["revenue_max"])){
                    $min =  (int)($item["person"]["revenue_min"] * 100);
                    $max =   (int)($item["person"]["revenue_max"] * 100);
                    $rand = rand($min,$max) / 100;
                }else{
                    $rand = 0;
                }
            }else{
                $rand = $item["person"]["revenue_lock"];
            }
            $revenue = sprintf("%.2f", ($item["money"] * $rand / 100));
            if (empty($revenue)){
                return false;
            }
            $memberId = $item["member_id"];
            $member= $this->memberService->dao->model->where('id', $memberId)->find();
            $memberCoin = $this->memberCoinService->dao->model->where('member_id',$memberId)->find();
            $memberBalance = $memberCoin['balance'];
            Db::startTrans();

            #收益不入账户余额，需入当前跟单
            $res[] = $memberCoin->save([
//                'balance'=>Db::raw('balance+'.($revenue)),
                "follow_total_revenue"=>Db::raw('follow_total_revenue+'.($revenue)),
                'update_time' => time(),
            ]);

            $res[] = $this->dao->model->where("id",$item["id"])->save([
                'total_revenue'=>Db::raw('total_revenue+'.($revenue)),
                'money'=>Db::raw('money+'.($revenue)),
                'yesterday_revenue'=>$revenue,
                "update_time"=>time()
            ]);
            $res[] = $this->memberService->deleteCacheDetail($memberId);

            $res[] = $this->waterService->dao->model->insert([
                'member_id' => $memberId,
                'agent_id' => $member['agent_id'],
                'money' => $revenue,
                'remark' => '',
                'source' => SourceEnum::FOLLOW_REVENUE,
                'source_id' => $item["id"],
                'pay_type' => WithdrawalTypeEnum::FOLLOW,
                'member_balance' => $item["money"],
                'balance' => ($item["money"] + $revenue),
                'describe' => '跟单收益',
                'type'=>1,
                'create_time' => time(),
                'update_time' => time(),
            ]);

            //处理分佣
            if(!empty($member['people_id']) && $revenue > 0) {
                $peopleId = $member['people_id'];
                //一级
                if ($peopleId > 0 && !empty( $peopleMember = $this->memberService->dao->model->where('id', $peopleId)->find()) && !empty($item["person"]["commission1"])){
                        $peopleFeePrice = sprintf("%.2f", ($revenue * $item["person"]["commission1"] / 100));
                        $peopleMemberCoin = $this->memberCoinService->dao->model->where('member_id',$peopleId)->find();
                        $peopleMemberBalance = $peopleMemberCoin['balance'];
                        $res[] = $peopleMemberCoin->save([
                            'balance'=>Db::raw('balance+'.(float)$peopleFeePrice),
                            'update_time' => time(),
                        ]);
                        $res[] = $this->memberService->deleteCacheDetail($peopleId);
                        $res[] = $this->waterService->dao->model->insert([
                            'member_id' => $peopleId,
                            'agent_id' => $peopleMember['agent_id'],
                            'money' => $peopleFeePrice,
                            'remark' => '',
                            'source' => SourceEnum::FOLLOW_REVENUE_COMMISSION,
                            'source_id' => $item["id"],
                            'pay_type' => WithdrawalTypeEnum::BALANCE,
                            'member_balance' => $peopleMemberBalance,
                            'balance' => ((float)$peopleMemberBalance + (float)$peopleFeePrice),
                            'describe' => '跟单收益分佣',
                            'type'=>1,
                            'create_time' => time(),
                            'update_time' => time(),
                        ]);
                }
                $peopleId2 = $peopleMember['people_id'] ?? 0;
                //二级
                if ($peopleId > 0 && $peopleId2 > 0 && !empty( $peopleMember2 = $this->memberService->dao->model->where('id', $peopleId2)->find()) && !empty($item["person"]["commission2"])){

                    $peopleFeePrice = sprintf("%.2f", ($revenue * $item["person"]["commission2"] / 100));
                    $people2MemberCoin = $this->memberCoinService->dao->model->where('member_id',$peopleId2)->find();
                    $people2MemberBalance = $people2MemberCoin['balance'];
                    $res[] = $people2MemberCoin->save([
                        'balance'=>Db::raw('balance+'.(float)$peopleFeePrice),
                        'update_time' => time(),
                    ]);
                    $res[] = $this->memberService->deleteCacheDetail($peopleId2);
                    $res[] = $this->waterService->dao->model->insert([
                        'member_id' => $peopleId2,
                        'agent_id' => $peopleMember2['agent_id'],
                        'money' => $peopleFeePrice,
                        'remark' => '',
                        'source' => SourceEnum::FOLLOW_REVENUE_COMMISSION2,
                        'source_id' => $item["id"],
                        'pay_type' => WithdrawalTypeEnum::BALANCE,
                        'member_balance' => $people2MemberBalance,
                        'balance' => ((float)$people2MemberBalance + (float)$peopleFeePrice),
                        'describe' => '跟单收益分佣',
                        'type'=>1,
                        'create_time' => time(),
                        'update_time' => time(),
                    ]);
                }
            }
            if (!empty($res) && ArrayHelper::checkArr($res)) {
                Db::commit();
                app(PushWebSockQueueService::class)->createMemberMessage(['type'=>'createFollowRevenue','member_id'=>$memberId]);
                return true;
            } else {
                Db::rollback();
                return false;
            }
        }
        return true;
    }




    public function getList($params = [],$agentIds = [])
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['create_time'][0])&&!empty($params['create_time'][1])){
            $filter[] = ['create_time','>=',strtotime($params['create_time'][0])];
            $filter[] = ['create_time','<=',strtotime($params['create_time'][1])+86400];
        }

        $sorter = 'create_time DESC';
        if(!empty($param['table_sorter'])){
            $sorter = json_decode($param['table_sorter'],true);
        }
        $queryModel = $this->dao->model->with(["person","member"=>["agent"]])
            ->when(!empty($params["username_like"]),function($query) use ($params,$agentIds){
                $query->whereIn("member_id",function ($query1) use ($params){
                    $map = [];
                    if (!empty($params['username_like'])){
                        $map[] = ['real_name|username|mobile', 'like', "%{$params['username_like']}%"];
                    }
                    return $query1->name("member")->where($map)->field('id');
                });
            })

            ->when(!empty($params['agent_id']) && $params['agent_id'] !== 'all' ,function($query) use ($params,$agentIds){
                $query->whereIn("member_id",function ($query1) use ($params,$agentIds){
                    return $query1->name("member")->where('agent_id', is_array($params['agent_id'])?'in':'=', $params['agent_id'])->field('id');
                });
            })->when(empty($params['agent_id']) || $params['agent_id'] === 'all' ,function($query) use ($params,$agentIds){
                $query->whereIn("member_id",function ($query1) use ($params,$agentIds){
                    return $query1->name("member")->where('agent_id', "in",$agentIds)->field('id');
                });
            })



            ->when(!empty($params["person_like"]),function($query) use ($params){
            $query->whereIn("person_id",function ($query1) use ($params){
                $map = [];
                if (!empty($params['person_like'])){
                    $map[] = ['nickname', 'like', "%{$params['person_like']}%"];
                }
                return $query1->name("follow_person")->where($map)->field('id');
            });
        })
        ->when($params["status"] !== "all" && $params["status"] !== '',function ($query) use ($params){
            $query->where("status",$params["status"]);
            })->where($filter)->order($sorter);

        $list = $queryModel->page($page)->paginate($limit)->toArray();

        $list["money_count"] = $queryModel->sum("money");
        $list["total_revenue"] = $queryModel->sum("total_revenue");

        return $list;
    }
}