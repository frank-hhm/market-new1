<?php
/**
 * @Date: 2025/7/6 17:06
 */
declare(strict_types=1);
namespace app\common\command;

use app\common\enum\finance\SourceEnum;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Config;
use think\facade\Db;


class TurntableCheckCommand extends Command
{

    protected function configure()
    {
        // 指令配置
        $this->setName('TurntableCheckCommand');
    }

    protected function execute(Input $input, Output $output): void
    {
        $TurntableSelect = Db::name('member')->where('turntable', '>', 0)->select();
        $time = time();
        $startTime = $time - 48 * 60 * 60;
        echo "开始执行 ---【".date('Y-m-d H:i:s',$startTime)."】-【".date('Y-m-d H:i:s',$time)."】\n";
        $memberCount = 0;
        foreach ($TurntableSelect as $key => $value){
            $newCount = 0;
            $round = 0;
            $count = Db::name('finance_member_recharge')
                ->where('member_id', $value['id'])
                ->where('source', SourceEnum::RECHARGE)
                ->where('status', 1)
                ->where('turntable','>', 0)
                ->where('update_time','>=',$startTime)->limit($value['turntable'])->order('id',  'desc')->sum('turntable');
            if($count > 0){
                $round = Db::name('turntable_record_log')->where('member_id', $value['id'])->where('status', 1)->where('create_time','>=',$startTime)->count();
                $newCount = $count - $round;
            }
            echo "会员【".$value['id']."】总:".$count."已用：".$round."次"."  还剩：".$newCount."次\n";
            $newCount < 0 && $newCount = 0;
            Db::name('member')->where('id', $value['id'])->update([
                'turntable' => $newCount
            ]);
            $memberCount++;
        }
        echo "处理会员总数：".$memberCount."\n";
//        dump($TurntableSelect);
//        dump("test");die;

    }
}