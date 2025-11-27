<?php
/**
 * @Date: 2025/6/25 22:59
 */
declare(strict_types=1);
namespace addon\smsbao\event;


use addon\smsbao\services\SmsService;
use app\common\exception\CommonException;

/**
 * 短信发送
 */
class SendSms
{

    /**
     * 短信发送
     */
    public function handle($param)
    {
        $smsService = app(SmsService::class);
        if($smsService->send($param)){
            return true;
        }
        throw new CommonException($smsService->getError()?:'未知错误');
    }
}