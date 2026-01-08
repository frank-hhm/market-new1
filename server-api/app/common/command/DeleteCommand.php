<?php
/**
 * @Date: 2025/5/21 19:44
 */
declare(strict_types=1);

namespace app\common\command;

use app\api\services\member\MemberService;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\activity\TurntableRecordLogService;
use app\common\services\common\CacheService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\MemberRechargeService;
use app\common\services\finance\MemberWithdrawalService;
use app\common\services\finance\WaterService;
use app\common\services\follow\PersonService;
use app\common\services\order\OrderLogService;
use app\common\services\order\OrderPingCangLogService;
use app\common\services\order\OrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class DeleteCommand extends Command
{
    protected function configure()
    {
        $this->setName('TestCommand');
    }

    protected function execute(Input $input, Output $output)
    {
//        $OrderService = app(\app\common\services\follow\OrderService::class);
//        $memberServer = app(MemberService::class);
//        $orderSelect = $OrderService->dao->model->select()->toArray();
//        $res = [];
//        foreach ($orderSelect as $item){
//             $detail = $memberServer->dao->model->where("id", $item["member_id"])->find();
//            if(!empty($detail) && $detail["moni"] == 1){
//                $OrderService->dao->model->where("member_id", $item["member_id"])->delete();
//                $res[] = $item["member_id"];
//            }
//        }
//        dump($res);die;
//        $memberIds = $memberServer->dao->model->column('id');
//        $memberIds = [109,148,110];

//        dump($memberIds);die;
        //余额
//        app(MemberCoinService::class)->dao->model->where('member_id','in',$memberIds)->update([
//            'balance'=>100,
//            'commission_total'=>0,
//            'commission_balance'=>0,
//            'fee_cash_commission_total'=>0,
//        ]);
        try {
            Db::startTrans();

            $createTime = StringHelper::_strtotime("2025-09-01 00:00:00");
            //充值
            $memberRecharge = app(MemberRechargeService::class);
            $status = $memberRecharge->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status;
            if($status){
                echo "删除会员充值记录成功\n";
            }

            $memberWithdrawal = app(MemberWithdrawalService::class);
            $status1 = $memberWithdrawal->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status1;
            if($status1){
                echo "删除会员提现记录成功\n";
            }

            $financeMemberFeeCashWater = app(MemberFeeCashWaterService::class);
            $status1s = $financeMemberFeeCashWater->dao->model
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status1s;
            if($status1s){
                echo "删除会员提现记录成功\n";
            }

            $water = app(WaterService::class);
            $status2 =  $water->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status2;
            if($status2){
                echo "删除会员流水记录成功\n";
            }
//            $memberCard = app(MemberCardService::class);
//            $memberCard->dao->model->where('member_id','in',$memberIds)->delete();
//
            $memberCommissionWater = app(MemberCommissionWaterService::class);
            $status31 = $memberCommissionWater->dao->model->where("create_time","<",$createTime)->delete();
            $res[] = $status31;
            if ($status31){
                echo "删除用户手续费返现记录成功".PHP_EOL;
            }

            $order = app(OrderService::class);
            $status3 = $order->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status3;
            if ($status3){
                echo "删除订单成功".PHP_EOL;
            }

            $orderLog = app(OrderLogService::class);
            $status4= $orderLog->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status4;
            if ($status4){
                echo "删除订单Log成功".PHP_EOL;
            }

            $orderLog = app(OrderPingCangLogService::class);
            $status5= $orderLog->dao->model
//                ->where('member_id','in',$memberIds)
                ->where("create_time","<",$createTime)->delete();
            $res[] = $status5;
            if ($status5){
                echo "删除订单PingCangLog成功".PHP_EOL;
            }
            $turntableRecordLog = app(TurntableRecordLogService::class);
            $status5s = $turntableRecordLog->dao->model ->where("create_time","<",$createTime)->delete();
            $res[] = $status5s;
            if ($status5s){
                echo "删除抽奖成功".PHP_EOL;
            }
            Db::commit();
            echo "删除成功".PHP_EOL;
            if(ArrayHelper::checkArr($res)){
            }else{
//                Db::rollback();
            }

        }catch (\Exception $e){
            Db::rollback();
            dump($e->getMessage());
        }

        echo 'success';

        die;
    }

}