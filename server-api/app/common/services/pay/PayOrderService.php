<?php
/**
 * @Date: 2024/11/7 21:05
 */
declare(strict_types=1);

namespace app\common\services\pay;

use app\common\dao\pay\PayOrderDao;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\services\BaseService;
use app\common\services\finance\WaterService;
use app\common\services\member\MemberCoinService;
use app\common\services\member\MemberService;
use think\facade\Db;

/**
 * 支付表
 * Class PayOrderService
 */
class PayOrderService extends BaseService
{


    /**
     * PayOrderService constructor.
     * @param PayOrderDao $dao
     */
    public function __construct(PayOrderDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->search($filter)->find();
        if (!$detail) {
            throw new CommonException('订单不存在');
        }
        return $detail;
    }

    /**
     * 新增单
     */
    public function execute($payPrice,$usdt,$payType = 'web',$memberId = 0)
    {
        $data['pay_price'] = $payPrice;
        $data['usdt'] = $usdt;
        $data['member_id'] = $memberId;

        $outNo = $this->dao->createNo();
        $data['order_no'] = $outNo;
        $find = $this->dao->create($data);
        if(empty($find->id)){
            throw new CommonException('订单生成失败');
        }
        $resData = event('Alipay',[
            'id'=>$find->id,
            'description'=>"充值",
            'pay_price'=>$payPrice,
            'order_no'=>$outNo,
            'pay_type'=>$payType
        ]);

        if($payType === 'app'){
//            $config = explode('&',$resData);
//            $_config = [];
//            foreach ($config as $k=>$v){
//                $arr = explode('=',$v);
//                $_config[] = [
//                    $arr[0] => $arr[1],
//                ];
//            }
//            $_config = ArrayHelper::array2json($_config);
//            dump($_config);die;
            $res['config'] = $resData;
        }else{
            $res['config'] = $resData;
        }
        $res['order_id'] = $find->id;
        $res['order_no'] = $find->order_no;
        return $res;
    }

    public function onPaySuccess($payData = [])
    {
        $detail = $this->dao->model->where([
            'order_no'=>$payData['order_no']
        ])->find();
        if(empty($detail)){
            throw new CommonException('订单不存在');
        }
        Db::startTrans();
        try {
            $res[] = $detail->save([
                'transaction_id'=>$payData['transaction_id'],
                'pay_price'=>$payData['pay_price'],
                'status'=>1,
                'update_time'=>time(),
            ]);

            $member =  app(MemberService::class)->dao->model->where(['id' => $detail['member_id']])->find();
            #到账及记录流水
            $memberCoin = app(MemberCoinService::class)->dao->model->where(['member_id' => $detail['member_id']])->find();
            $memberBalance = 0;
            if($member['status']['value'] == 1 && !empty($memberCoin) ){
                $res[] = app(MemberCoinService::class)->dao->model->where(['member_id' => $detail['member_id']])->save([
                    'balance'=>Db::raw('balance+'.$detail['usdt'])
                ]);
                app(MemberService::class)->deleteCacheDetail($detail['member_id']);
                $memberBalance = $memberCoin['balance'];
                $res[]  =  app(WaterService::class)->dao->model->create([
                    'member_id' => $detail['member_id'],
                    'agent_id' => $member['agent_id'],
                    'money' => $detail['usdt'],
                    'remark' =>  '',
                    'source' => 'recharge',
                    'source_id' =>$detail['id'],
                    'pay_type' => RechargePayTypeEnum::ALIPAY,
                    'member_balance' => $memberBalance,
                    'balance' => $memberBalance + $detail['usdt'],
                    'describe' => "支付宝在线充值",
                    'type'=>1
                ]);
                $memberBalance = $memberBalance + $detail['usdt'];
            }
            app(MemberService::class)->deleteCacheDetail( $detail['member_id']);
            if (!empty($res) && ArrayHelper::checkArr($res)){
                Db::commit();
                return true;
            }
            Db::rollback();
            return false;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            dump($e);die;
            Db::rollback();
            return false;
        }
    }
}
