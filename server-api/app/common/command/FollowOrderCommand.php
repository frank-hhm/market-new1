<?php
declare(strict_types=1);

namespace app\common\command;
use app\common\helper\StringHelper;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\follow\OrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class FollowOrderCommand extends Command
{
    protected function configure()
    {
        $this->setName('FollowOrderCommand');
    }

    protected function execute(Input $input, Output $output)
    {
        echo "FollowOrderCommand\n";
        echo "跟单收益 开始执行：". date("Y-m-d H:i:s", time());
        $res = app(OrderService::class)->checkOrder();
        echo "跟单收益 执行完成 "."成功[".($res['success'])."]  "."失败[".($res["error"])."]  ". date("Y-m-d H:i:s", time())."\n";

    }
}