<?php
/**
 * @Date: 2025/6/27 19:14
 */
declare(strict_types=1);

namespace app\common\services\order;

use app\common\dao\order\OrderLogDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;
use think\facade\Filesystem;
/**
 * 订单记录
 * Class OrderLogService
 */
class OrderLogService extends BaseService
{

    /**
     * OrderLogService constructor.
     * @param OrderLogDao $dao
     */
    public function __construct(OrderLogDao $dao)
    {
        $this->dao = $dao;
    }


    public function getOrderCountAll($pid = 0,$ostyle = 1){
        return $this->dao->model->where([
            'pid'=>$pid,
            'ostyle'=>$ostyle
        ])->count();
    }
}