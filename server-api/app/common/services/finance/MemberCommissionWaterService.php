<?php
/**
 * @Date: 2025/3/2 9:35
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\MemberCommissionWaterDao;
use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use think\facade\Db;

/**
 * 佣金日记
 * Class MemberCommissionWaterService
 */
class MemberCommissionWaterService extends BaseService
{
    /**
     * MemberCommissionWaterService constructor.
     * @param MemberCommissionWaterDao $dao
     */
    public function __construct(MemberCommissionWaterDao $dao)
    {
        $this->dao = $dao;
    }


    //结算
    public function settlement($id = 0){

        $water = $this->dao->model->where('order_id',$id)->where('status',0)->find();

        if($water){
            Db::startTrans();
            try{
                    $memberCoin = app(MemberCoinService::class)->dao->model->where(['member_id' => $water['people_id']])->find();
                    $memberBalance = 0;
                    #到账及记录流水
                    $res[] = app(MemberCoinService::class)->dao->model->where(['member_id' => $water['people_id']])->save([
                        'commission_balance'=>Db::raw('commission_balance+'.$water['money']),
                        'commission_total'=>Db::raw('commission_total+'.$water['money'])
                    ]);
                    app(MemberService::class)->deleteCacheDetail($water['people_id']);
                    $memberBalance = $memberCoin['commission_balance'];
                    $res[]  =  app(WaterService::class)->create([
                        'member_id' => $water['people_id'],
                        'agent_id' => $water['agent_id'] ?? 0,
                        'money' => $water['money'],
                        'remark' => '佣金结算',
                        'source' => 'commission_fee',
                        'source_id' =>$id,
                        'pay_type' => 'commission_balance',
                        'member_balance' => $memberBalance,
                        'balance' => $memberBalance + $water['money'],
                        'describe' => '佣金结算',
                        'type'=>1
                    ]);

                    $res[] = $this->dao->model->where('id',$water['id'])->update([
                        'status'=>1,
                        'update_time'=>time()
                    ]);
                app(MemberService::class)->deleteCacheDetail( $water['people_id']);
                if (!empty($res) && ArrayHelper::checkArr($res)){
                    Db::commit();
                    return true;
                }
                Db::rollback();
                return false;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                Db::rollback();
                return false;
            }
        }else{
            return true;
        }
    }
}