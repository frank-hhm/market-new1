<?php
/**
 * @Date: 2025/7/6 14:42
 */
declare(strict_types=1);

namespace app\agentApi\validate;

use think\Validate;

class AgentPassValidate extends Validate
{
    protected $rule = [
        'old_pwd' => 'requireCallback:check_require|min:5',
        'pwd' => 'requireCallback:check_require|min:5',
        'conf_pwd' => 'requireCallback:check_require|confirm:pwd|min:5',
    ];
    protected $message  =   [
        'old_pwd.requireCallback'   => '请输入账号密码!',
        'old_pwd.min'   => '密码最小长度为6位!',
        'pwd.requireCallback'   => '请输入账号密码!',
        'conf_pwd.requireCallback'   => '请输入账号确定密码!',
        'pwd.min'   => '密码最小长度为6位!',
        'conf_pwd.min'   => '确定密码最小长度为6位!',
        'conf_pwd.confirm'   => '两次密码不一致!',
    ];

    protected function check_require($value, $data){
        return true;
    }
}
