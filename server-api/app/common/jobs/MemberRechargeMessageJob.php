<?php
/**
 * @Date: 2025/6/28 15:50
 */
declare(strict_types=1);
namespace app\common\jobs;
use app\common\helper\MailerHelper;
use app\common\library\party\email\MailerService;
use think\facade\Db;
use think\queue\Job;

/**
 * 充值通知
 */
class MemberRechargeMessageJob
{
    # php think queue:listen --queue MemberRechargeMessageJob
    /**
     * fire是消息队列默认调用的方法
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
            return true;
        }

        if ($this->doJob($data)) {
            $job->delete();
        }
        return true;
    }

    public function doJob($data)
    {
        $email = sysconf("member_recharge_message_email");
        if (empty($email)){
            return  true;
        }
        $mailService = app(MailerService::class);
        $mailService->addAddress($email, $email);
        $tempRes = MailerHelper::getTemplate("member-recharge-message",$email);
        !empty($tempRes['html']) && $mailService->setHtml(true);
        // 发送邮件
        if ($mailService->send($tempRes['title'], $tempRes['content'])) {
            return false;
        }
        return true;
    }
}