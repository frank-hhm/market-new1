<?php
/**
 * @Date: 2025/6/26 0:59
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\common\dao\member\MemberDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\activity\TurntableRecordLogService;
use app\common\services\BaseService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\MemberRechargeService;
use app\common\services\finance\MemberWithdrawalService;
use app\common\services\finance\WaterService;
use app\common\services\order\OrderLogService;
use app\common\services\order\OrderService;
use think\facade\Db;

/**
 * Class MemberAgentService
 */
class MemberAgentService extends BaseService
{

    /**
     * MemberAuthService constructor.
     * @param MemberDao $dao
     */
    public function __construct(MemberDao $dao)
    {
        $this->dao = $dao;
    }

    public function updateAgent($memberId, $agentId)
    {

        $detail = app(MemberService::class)->getDetail($memberId);
        if(empty($detail)){
            throw new CommonException('会员不存在!');
        }
        $updateTimeArr = [
            'update_agent_time'=>time()
        ];
        try {
            Db::startTrans();
            $res[] = $this->dao->model->where(['id'=>$memberId])->update(array_merge([
                'agent_id'=>$agentId,
            ],$updateTimeArr));
            //
            $res[] = $this->dao->model->where([
                'id'=>$detail['moni_id'],
            ])->update(array_merge([
                'agent_id'=>$agentId,
            ],$updateTimeArr));

            $financeMemberFeeCashWater = app(MemberFeeCashWaterService::class);
            if($financeMemberFeeCashWater->dao->model->where([
                    'member_id'=>$memberId,
                ])->limit(1)->count() > 0){
                $res[] = $financeMemberFeeCashWater->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $memberRecharge = app(MemberRechargeService::class);
            if($memberRecharge->dao->model->where([
                    'member_id'=>$memberId,
                ])->limit(1)->count() > 0){
                $res[] = $memberRecharge->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $memberWithdrawal = app(MemberWithdrawalService::class);
            if($memberWithdrawal->dao->model->where([
                    'member_id'=>$memberId,
                ])->limit(1)->count() > 0){
                $res[] = $memberWithdrawal->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $water = app(WaterService::class);
            if($water->dao->model->where([
                    'member_id'=>$memberId,
                ])->limit(1)->count() > 0){
                $res[] = $water->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $memberCard = app(MemberCardService::class);
            if ($memberCard->dao->model->where(['member_id'=>$memberId])->limit(1)->count() > 0){
                $res[] = $memberCard->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $memberCommissionWater = app(MemberCommissionWaterService::class);
            if ($memberCommissionWater->dao->model->where(['member_id'=>$memberId])->limit(1)->count() > 0){
                $res[] = $memberCommissionWater->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $order = app(OrderService::class);
            if ($order->dao->model->where(['member_id'=>$memberId])->limit(1)->count() > 0){
                $res[] = $order->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $orderLog = app(OrderLogService::class);
            if ($orderLog->dao->model->where(['member_id'=>$memberId])->limit(1)->count() > 0){
                $res[] = $orderLog->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            $turntableRecordLog = app(TurntableRecordLogService::class);
            if ($turntableRecordLog->dao->model->where(['member_id'=>$memberId])->limit(1)->count() > 0){
                $res[] = $turntableRecordLog->dao->model->where([
                    'member_id'=>$memberId,
                ])->update(array_merge([
                    'agent_id'=>$agentId,
                ],$updateTimeArr));
            }

            if(ArrayHelper::checkArr($res)){
                Db::commit();
                return true;
            }
            Db::rollback();
            return false;
        }catch (\Exception $e){
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

}