<?php
/**
 * @Date: 2025/5/21 19:44
 */
declare(strict_types=1);

namespace app\common\command;

use app\api\services\member\MemberService;
use app\common\constants\CacheKeyConstant;
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
use Psr\SimpleCache\InvalidArgumentException;
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
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);

        $key = "{queues:CheckMemberSubscribeJob}";

        try {
            if ($cacheService->has($key)) {
                if ($cacheService->delete($key)) {
                    echo "CheckMemberSubscribeJob记录----删除成功\n";
                } else {

                    echo "CheckMemberSubscribeJob记录----删除失败\n";
                }
            } else {
                echo "无CheckMemberSubscribeJob记录\n";
            }
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage()."\n";
        }
        return true;
    }
}