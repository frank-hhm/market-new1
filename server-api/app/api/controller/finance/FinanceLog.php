<?php
/**
 * @Date: 2025/6/29 19:02
 */
declare(strict_types=1);
namespace app\api\controller\finance;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use think\facade\App;
use app\common\services\finance\WaterService;

/**
 * Class FinanceLog
 */
class FinanceLog extends \app\api\controller\Base
{
    /**
     * WaterService constructor.
     * @param App $app
     * @param WaterService $service
     */
    public function __construct(App $app, WaterService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->getMore([
            ['cointype', 1],
            ['source','']
        ]);
        $data['member_id'] = $this->uid;
        $this->success("获取成功",$this->service->getListApi($data));
    }

    public function getWaterList(){
        $data = $this->request->getMore([
            "start_date",
            "end_date"
        ]);
        $data['member_id'] = $this->uid;
        $this->success("获取成功",$this->service->getListApi($data,[SourceEnum::RECHARGE,SourceEnum::MEMBER_WITHDRAWAL],[
            RechargePayTypeEnum::BALANCE
        ]));
    }

    public function getCommissionWaterList(){
        $data = $this->request->getMore([
            "start_date",
            "end_date"
        ]);
//        $data['member_id'] = $this->uid;
        $this->success("获取成功",$this->service->getListApi($data,[SourceEnum::COMMISSION_FEE,SourceEnum::MEMBER_COMMISSION_WITHDRAWAL],[
            RechargePayTypeEnum::COMMISSION_BALANCE
        ]));
    }

    public function getFollowWaterList(){
        $data = $this->request->getMore([
            "start_date",
            "end_date"
        ]);
//        $data['member_id'] = $this->uid;
        $this->success("获取成功",$this->service->getListApi($data,[SourceEnum::FOLLOW_REVENUE,SourceEnum::FOLLOW,SourceEnum::FOLLOW_CLOSE],[
            RechargePayTypeEnum::COMMISSION_BALANCE,
            RechargePayTypeEnum::FOLLOW_BALANCE
        ]));
    }


}