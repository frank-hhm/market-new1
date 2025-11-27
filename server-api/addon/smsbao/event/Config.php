<?php
/**
 * @Date: 2025/6/25 22:59
 */
declare(strict_types=1);
namespace addon\smsbao\event;
/**
 * 短信配置
 */
class Config
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle($param)
    {
        $config = [
            'addon_sms'
        ];

        return ['addon_sms'=>$config];
    }
}