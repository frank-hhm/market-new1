<?php
/**
 * @Date: 2025/6/29 21:02
 */
declare(strict_types=1);
namespace app\api\controller\follow;
use app\api\services\follow\OrderService;
use think\facade\App;
use app\api\services\follow\PersonService;

/**
 * Class 跟单订单
 */
class Order extends \app\api\controller\Base
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
     * 获取我的跟单
     * @method(GET)
     */
    public function getList()
    {
        $data = $this->request->getMore([
           [ "status",1]
        ]);
        $this->success("获取成功",$this->service->getMemberOrderListApi($this->uid,$data));
    }
}