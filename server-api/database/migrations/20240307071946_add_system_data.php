<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddSystemData extends Migrator
{
    public function change()
    {
        sysconf('order_min_price',1);
        sysconf('order_max_price',100000);
        sysconf('addon_alisms','{"access_key_id":"","access_key_secret":"","temp_sign":"阿里云短信测试","alisms_temps":[{"temp_name":"动态码登录","temp_code":"verify_code","temp_param":"code","temp_content":"您的验证码为：{code}，该验证码 5 分钟内有效，请勿泄漏于他人！","temp_id":"SMS_154950909"}]}');
        sysconf('about_bg','');
        sysconf('kefu_qq','');
        sysconf('cnshuhai_username','');
        sysconf('cnshuhai_password','');
        sysconf('cnshuhai_subscribe','HI,WA,WE,WX');

        sysconf('payment_bank_status',0);
        sysconf('payment_bank_real_name','');
        sysconf('payment_bank_code','');
        sysconf('payment_bank_name','');
        sysconf('payment_bank_child','');
        sysconf('payment_alipay_real_name','');
        sysconf('payment_alipay_account','');
        sysconf('payment_alipay_status',1);
        sysconf('order_min_price',20);
        sysconf('order_max_price',10000);
        sysconf('agent_withdrawal_rate',2);
        sysconf('member_withdrawal_rate',2);
        sysconf('activitys','[{"id":1,"title":"注册送","desc":"注册即送50000","money":50000,"src":"\/pages\/login\/register","type":"register"},{"id":2,"title":"认证送","desc":"认证即送600","money":600,"src":"\/pages\/member\/recognize","type":"bind"},{"id":3,"title":"首入送","desc":"首入存即送1000","money":1000,"src":"\/pages\/member\/deposit","type":"deposit"}]');
        sysconf('agreement_customer','');
        sysconf('agreement_risk','');
        sysconf('agreement_disclaimers','');
    }
}
