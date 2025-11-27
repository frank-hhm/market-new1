<?php
/**
 * @Date: 2025/6/30 16:52
 */
declare(strict_types=1);
namespace app\sys\controller\order;

use app\api\services\member\MemberService;
use app\common\enum\EnumFactory;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\order\MemberOrderService;
use app\common\services\ProductCacheService;
use app\common\services\ProductService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\order\OrderService;
use think\facade\Log;

/**
 * 订单
 * Class Order
 * @package app\sys\controller
 */
class Order extends Base
{
    /**
     * Order constructor.
     * @param App $app
     * @param OrderService $service
     */
    public function __construct(App $app, OrderService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 期货平仓列表
     * @method(GET)
     */
    public function pinList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle',''],
            ['selltime',[]],
            ['buytime',[]],
            ['agent_id',[]],
            ['is_wei','all'],
            ["pid",[]]
        ]);
        $data['type'] = 1;
        $agentIds = $this->getAgentChildIds();
        $res = $this->service->getPinList($data,$agentIds);
        if(isset($res['money_profit'])){
            $res['money_profit'] =  sprintf("%.2f",$res['money_profit']);
        }
        if(isset($res['money_sx_fee'])){
            $res['money_sx_fee'] =  sprintf("%.2f",$res['money_sx_fee']);
        }
        if(isset($res['onumber'])){
            $res['onumber'] =  sprintf("%.2f",$res['onumber']);
        }
        $this->success('获取成功',$res);
    }
    /**
     * 期货持仓列表
     * @method(GET)
     */
    public function chiList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle',''],
            ['buytime',[]],
            ['agent_id',[]],
            ['is_wei','all'],
            ["pid",[]]
        ]);
        $data['type'] = 1;
        $agentIds = $this->getAgentChildIds();
        $res = $this->service->getChiList($data,$agentIds);
        if(isset($res['onumber'])){
            $res['onumber'] =  sprintf("%.2f",$res['onumber']);
        }
        $this->success('获取成功',$res);
    }

    /**
     * 模拟平仓列表
     * @method(GET)
     */
    public function pinMoniList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle',''],
            ['selltime',[]],
            ['buytime',[]],
            ['agent_id',[]],
            ['is_wei','all']
        ]);

        $agentIds = $this->getAgentChildIds();
        $agentIds[] = 0;
        $data['type'] = 1;

        $res = $this->service->getPinList($data,$agentIds,1);
        if(isset($res['money_profit'])){
            $res['money_profit'] =  sprintf("%.2f",$res['money_profit']);
        }
        if(isset($res['money_sx_fee'])){
            $res['money_sx_fee'] =  sprintf("%.2f",$res['money_sx_fee']);
        }
        if(isset($res['onumber'])){
            $res['onumber'] =  sprintf("%.2f",$res['onumber']);
        }
        $this->success('获取成功',$res);
    }

    /**
     * 模拟持仓列表
     * @method(GET)
     */
    public function chiMoniList()
    {
        $data = $this->request->postMore([
            ['username_like', ''],
            ['ostyle',''],
            ['buytime',[]],
            ['agent_id',[]],
            ['is_wei','all']
        ]);

        $agentIds = $this->getAgentChildIds();
        $agentIds[] = 0;
        $data['type'] = 1;


        $res = $this->service->getChiList($data,$agentIds,1);
        if(isset($res['onumber'])){
            $res['onumber'] =  sprintf("%.2f",$res['onumber']);
        }
        $this->success('获取成功',$res);
    }

    /**
     * 平仓
     * @method(PUT)
     */
    public function pingSell()
    {
        $params = $this->request->getMore([
            ['oid', ''],
            ['member_id', ''],
        ]);
        $oid = $params['oid'];
        $memberOrderService = app(MemberOrderService::class);
        if($res = $memberOrderService->pingCang($params['member_id'],$oid,SellTypeEnum::ADMIN)){
            $this->success('平仓成功！',$res);
        }else{
            $this->error($memberOrderService->getError()?:'平仓失败，请重试！',[],$memberOrderService->getErrorCode()?:0);
        }
    }

}