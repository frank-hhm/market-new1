<?php
/**
 * @Date: 2025/6/29 21:02
 */
declare(strict_types=1);
namespace app\api\controller\follow;
use app\api\services\follow\OrderService;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\helper\StringHelper;
use app\common\services\order\MemberOrderService;
use think\facade\App;
use app\api\services\follow\PersonService;

/**
 * Class 交易员
 */
class Person extends \app\api\controller\Base
{
    /**
     * Person constructor.
     * @param App $app
     * @param PersonService $service
     */
    public function __construct(App $app, PersonService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 列表
     * @method(GET)
     */
    public function getList()
    {
        $data = $this->request->getMore([
            ['sort','']
        ]);
        $this->success("获取成功",$this->service->getListApi($data,$this->uid));
    }
    /**
     * 详细
     * @method(GET)
     */
    public function getDetail()
    {
        $params = $this->request->getMore([
            ['id', ''],
        ]);

        $detail = $this->service->getDetail($params['id']);
        $orderService = app(OrderService::class);
        $followOrder = $orderService->getFollowOrder($this->uid,$params["id"]);
        $detail["follow_status"] = !empty($followOrder);
        $detail["follow_order"] = $followOrder;
        $detail["member_count"] = $orderService->getMemberCountByPerson($params["id"]);
        $this->success('获取成功',$detail);
    }


    public function getTradingList(){
        $params = $this->request->getMore([
            ['id', ''],
            ["ostaus",0]
        ]);
        if (empty($params["id"])){
            $this->error("参数错误");
        }
        if (!in_array($params["ostaus"],[0,1])){
            $this->error("参数错误");
        }

        $list = $this->service->getTradingList($params['id'],$params["ostaus"]);
        $this->success('获取成功', $list);
    }


    public function createOrder(){
        $params = $this->request->getMore([
            ['id', ''],
            ["money",0]
        ]);
        if(empty($params["money"])){
            $this->error('投入跟单金额不能为空！');
        }
        if(!is_numeric($params["money"])){
            $this->error('投入跟单金额错误！');
        }
        if(empty($params["id"])){
            $this->error('选择交易员错误！');
        }

        $balance = $this->member['balance'];
        $MemberTrade = app(MemberOrderService::class)->getMemberTradeInfo($this->uid);
        if($params['money'] > $balance - $MemberTrade['baozhengjin_sum']){
            $this->error('可用余额不足！');
        }
        $service = app(OrderService::class);
        if( $service->createOrder($this->uid,$params)){
            $this->success('跟单成功!');
        }
        $this->error($service->getError()?:'跟单失败!');
    }

    /**
     * 结束订单状态
     * @method(POST)
     */
    public function endStatus()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $service = app(OrderService::class);
        if ($service->onEndStatus(0,$id, $this->uid)) {
            $this->success('处理成功');
        }
        $this->error($service->getError()?:'处理失败');
    }


    /**
     * 获取我的跟单
     * @method(GET)
     */
    public function getOrderList()
    {
        $data = $this->request->getMore([
        ]);
        $this->success("获取成功",$this->service->getMemberOrderListApi($this->uid,$data));
    }
}