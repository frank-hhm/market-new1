<?php
/**
 * @Date: 2025/7/6 17:05
 */
declare(strict_types=1);

namespace app\common\command;
use app\common\helper\StringHelper;
use app\common\services\finance\MemberFeeCashWaterService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class FinanceMemberFeeCashCommand extends Command
{
    protected function configure()
    {
        $this->setName('FinanceMemberFeeCashCommand');
    }

    protected function execute(Input $input, Output $output)
    {
        echo "FinanceMemberFeeCashCommand\n";
        echo "手续费返现 开始执行：". date("Y-m-d H:i:s", time());
        $res = app(MemberFeeCashWaterService::class)->checkOrder();
        echo "手续费返现 执行完成 "."成功[".($res['success'])."]  "."失败[".($res["error"])."]  ". date("Y-m-d H:i:s", time())."\n";

    }
}