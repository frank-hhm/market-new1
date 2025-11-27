<?php
/**
 * @Date: 2025/6/30 20:54
 */
declare(strict_types=1);
namespace app\sys\controller\order;

use app\api\services\member\MemberService;
use app\common\enum\EnumFactory;
use app\common\services\common\CacheService;
use app\common\services\ProductService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\order\OrderRobotService;

/**
 * 挂单记录
 * Class OrderRobot
 * @package app\sys\controller\order
 */
class OrderRobot extends Base
{
    /**
     * OrderRobot constructor.
     * @param App $app
     * @param OrderRobotService $service
     */
    public function __construct(App $app, OrderRobotService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 挂单列表
     * @method(GET)
     */
    public function guaList(){
        $data = $this->request->getMore([
            ['agent_id',[]],
            ['moni', 'all'],
        ]);
        $agentIds = $this->getAgentChildIds();
        $list = $this->service->getOrderRobotList(0,$data,$agentIds);
        $this->success('获取成功',$list);
    }
    /**
     * 批量撤单
     * @method(PUT)
     */
    public function close(){
        $params = $this->request->postMore([
            ['ids',[]],
        ]);
        if(empty($params['ids'])){
            $this->error('请选择要撤单的订单！');
        }
        if($this->service->close($params['ids'])){
            $this->success('操作成功');
        }else{
            $this->error($this->service->getError());
        }
    }


}