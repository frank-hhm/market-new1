<?php
/**
 * @Date: 2025/6/26 0:42
 */
declare(strict_types=1);

namespace app\api\validate;

use think\Validate;

class MemberValidate extends Validate
{
    protected $rule = [
//        'username' => 'require',
        'email'  => 'requireWithout:phone',
        'phone' => 'requireWithout:email',
        'pwd' => 'requireCallback:check_require|min:5',
        'conf_pwd' => 'requireCallback:check_require|confirm:pwd|min:5',
        'invite_code' => 'require',
        'code' => 'require|length:6',

    ];
    protected $message  =   [
//        'username.require' => '请输入用户名!',
        'pwd.requireCallback'   => '请输入账号密码!',
        'conf_pwd.requireCallback'   => '请输入账号确定密码!',
        'pwd.min'   => '账号最小长度为6位!',
        'conf_pwd.min'   => '账号确定密码最小长度为6位!',
        'conf_pwd.confirm'   => '两次密码不一致!',
        'invite_code.require' => '请输入邀请码!',
        'code.require' => '请输入验证码!',
        'code.length' => '验证码长度不对!',
    ];

    protected function check_require($value, $data){
        if(empty($data['id']) || !empty($data['pwd'])|| !empty($data['conf_pwd'])){
            return true;
        }
    }

    protected function roles_check_require($value, $data){
        if(empty($data['id'])){
            return true;
        }
    }
}
