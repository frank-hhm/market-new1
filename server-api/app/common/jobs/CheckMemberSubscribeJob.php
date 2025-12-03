<?php
/**
 * @Date: 2024/3/20 16:20
 */
declare(strict_types=1);
namespace app\common\jobs;

use app\common\constants\CacheKeyConstant;
use app\common\enum\order\SellTypeEnum;
use app\common\helper\MailerHelper;
use app\common\helper\ProductHelper;
use app\common\library\party\email\MailerService;
use app\common\services\common\ConsoleLogService;
use app\common\services\member\MemberSubscribeService;
use app\common\services\order\MemberOrderService;
use app\common\services\order\OrderService;
use app\common\services\ProductCacheService;
use think\facade\Log;
use think\queue\Job;
use think\Exception;

class CheckMemberSubscribeJob
{
    public function fire(Job $job, $data)
    {
        try {
            if ($this->checkSubscribe($data)) {
                $job->delete();
            } else {
                if ($job->attempts() > 5) {
                    $job->failed();
                } else {
                    $job->release(1);
                }
            }
        } catch (\Exception $e) {
            $job->failed($e);
        }
    }

    public function checkSubscribe($data)
    {
        try {

            $memberSubscribeService = app(MemberSubscribeService::class);
            $filter[] = ["source","=",$data["source"] ?? 0];
            $filter[] = ["source_id","=",$data["source_id"] ?? 0];

            $subscribeSelect = $memberSubscribeService->dao->model->with(["member"])->where($filter)->select()->toArray();

            $mailService = app(MailerService::class);
            $tempRes = MailerHelper::getTemplate("subscribe-message",$data['message']);
            dump($data,$subscribeSelect);
            !empty($tempRes['html']) && $mailService->setHtml(true);
            foreach ($subscribeSelect as $item){
                dump($item);
                if (!empty($item["member"]) && !empty($item["member"]['email'])){
                    $mailService->addAddress($item["member"]['email'], $item["member"]['real_name'] ?? $item["member"]['username']);
                    // 发送邮件
                    $mailService->send($tempRes['title'], $tempRes['content']);
                }
            }
            return true;
        }catch (\Exception $e){
            dump($e->getMessage());
            return false;
        }
    }

    public function failed($data)
    {
        // 记录任务执行失败日志
        Log::record('jobs: ' . json_encode($data), 'error');
    }
}