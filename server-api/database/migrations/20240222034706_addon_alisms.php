<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddonAlisms extends Migrator
{

    public function change()
    {
        $data = [
            'access_key_id'=>'',
            'access_key_secret'=>'',
            'temp_sign'=>'',
            'alisms_temps'=>[[
                'temp_name'=>'动态码登录',
                'temp_code'=>'verify_code',
                'temp_param'=>'code',
                'temp_content'=>'您的验证码为：{code}，该验证码 5 分钟内有效，请勿泄漏于他人！',
                "temp_id"=>"SMS_180052367"
            ]],
        ];
        sysconf('addon_alisms',json_encode($data,JSON_UNESCAPED_UNICODE));
    }
}
