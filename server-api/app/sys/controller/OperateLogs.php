<?php
/**
 * @Date: 2025/7/6 20:38
 */
declare(strict_types=1);
namespace app\sys\controller;

use think\facade\App;
use app\common\services\OperateLogsService;

/**
 * 日志
 * Class OperateLogs
 * @package app\sys\controller
 */
class OperateLogs extends Base
{
    /**
     * Payment constructor.
     * @param App $app
     * @param OperateLogsService $service
     */
    public function __construct(App $app, OperateLogsService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 日志列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
            ["source","admin"],
            ["account_like",""],
            ["time",[]]
        ]);
        $this->success('success', $this->service->getList($data));
    }
}