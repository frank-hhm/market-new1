<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddSystemData1 extends Migrator
{

    public function change()
    {
        sysconf('addon_onesms','{"access_key_id":"","access_key_secret":"","temp_sign":"短信签名","onesms_temps":[{"temp_name":"动态码登录","temp_code":"verify_code","temp_param":"code","temp_content":"您的验证码为：{code}，该验证码 5 分钟内有效，请勿泄漏于他人！","temp_id":"SMS_154950909"}]}');

    }
}
