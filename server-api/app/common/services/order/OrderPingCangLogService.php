<?php
/**
 * @Date: 2025/6/28 15:50
 */
declare(strict_types=1);
namespace app\common\services\order;

use app\common\dao\order\OrderPingCangLogDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;
use think\facade\Filesystem;
/**
 * 订单记录
 * Class OrderLogService
 */
class OrderPingCangLogService extends BaseService
{

    /**
     * OrderPingCangLogService constructor.
     * @param OrderPingCangLogDao $dao
     */
    public function __construct(OrderPingCangLogDao $dao)
    {
        $this->dao = $dao;
    }
}