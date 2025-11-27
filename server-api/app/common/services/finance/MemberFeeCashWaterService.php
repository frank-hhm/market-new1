<?php
/**
 * @Date: 2025/5/3 7:57
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\MemberFeeCashWaterDao;
use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use think\facade\Db;

/**
 * 手续费返现日记
 * Class MemberFeeCashWaterService
 */
class MemberFeeCashWaterService extends BaseService
{
    /**
     * MemberFeeCashWaterService constructor.
     * @param MemberFeeCashWaterDao $dao
     */
    public function __construct(MemberFeeCashWaterDao $dao)
    {
        $this->dao = $dao;
    }

    public function checkOrder(){
        $time = time();
        // 昨天的日期
        $yesterdayDate = date('Y-m-d', strtotime('-1 day', $time));
        $startTimestamp = strtotime($yesterdayDate . ' 00:00:00');
        $endTimestamp = strtotime($yesterdayDate . ' 23:59:59');
        $select = $this->dao->model
            ->where('settlement_status',0)
            ->where('settlement_time',0)
            ->where([
                ['order_time','>=',$startTimestamp],
                ['order_time','<=',$endTimestamp],
            ])
            ->select()->toArray();
        $successCount = 0;
        $errorCount = 0;
        foreach ($select as $item){
            if($this->settlement($item)){
                $successCount++;
            }else{
                $errorCount++;
            }
        }
        return ['success'=>$successCount,'error'=>$errorCount];
    }


    public function settlement($water){
        if($water){
            Db::startTrans();
            try{
                $memberCoin = app(MemberCoinService::class)->dao->model->where(['member_id' => $water['member_id']])->find();
                $memberBalance = 0;
                #到账及记录流水
                $res[] = app(MemberCoinService::class)->dao->model->where(['member_id' => $water['member_id']])->save([
                    'commission_balance'=>Db::raw('commission_balance+'.$water['money']),
                    'fee_cash_commission_total'=>Db::raw('fee_cash_commission_total+'.$water['money'])
                ]);
                app(MemberService::class)->deleteCacheDetail($water['member_id']);
                $memberBalance = $memberCoin['commission_balance'];
                $res[]  =  app(WaterService::class)->create([
                    'member_id' => $water['member_id'],
                    'agent_id' => $water['agent_id'] ?? 0,
                    'money' => $water['money'],
                    'remark' => '',
                    'source' => 'fee_cash',
                    'source_id' =>$water['source_id'],
                    'pay_type' => 'commission_balance',
                    'member_balance' => $memberBalance,
                    'balance' => $memberBalance + $water['money'],
                    'describe' => '手续费返现结算',
                    'type'=>1
                ]);

                $res[] = $this->dao->model->where('id',$water['id'])->update([
                    'settlement_status'=>1,
                    'settlement_time'=>time()
                ]);
                app(MemberService::class)->deleteCacheDetail( $water['member_id']);
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