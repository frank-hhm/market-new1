<?php
/**
 * @Date: 2025/5/21 19:44
 */
declare(strict_types=1);

namespace app\common\command;

use app\api\services\member\MemberService;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\activity\TurntableRecordLogService;
use app\common\services\common\CacheService;
use app\common\services\finance\MemberCommissionWaterService;
use app\common\services\finance\MemberFeeCashWaterService;
use app\common\services\finance\MemberRechargeService;
use app\common\services\finance\MemberWithdrawalService;
use app\common\services\finance\WaterService;
use app\common\services\follow\PersonService;
use app\common\services\order\OrderLogService;
use app\common\services\order\OrderPingCangLogService;
use app\common\services\order\OrderService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class DeleteMemberJobLogCommand extends Command
{
    protected function configure()
    {
        $this->setName('DeleteMemberJobLogCommand');
    }

    protected function execute(Input $input, Output $output)
    {
        $cacheService = app(CacheService::class);

        $key = "{queues:CheckMemberSubscribeJob}";

        if($cacheService->has($key)){
            if($cacheService->delete($key)){
                echo "CheckMemberSubscribeJob记录----删除成功\n";
            }else{

                echo "CheckMemberSubscribeJob记录----删除失败\n";
            }
        }else{
            echo "无CheckMemberSubscribeJob记录\n";
        }
        return true;
    }
}