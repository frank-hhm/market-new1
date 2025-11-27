<?php
/**
 * @Date: 2025/6/26 0:36
 */
declare(strict_types=1);

namespace app\api\controller;
use app\api\services\member\MemberService;
use app\api\validate\MemberValidate;
use app\BaseController;
use app\common\exception\CommonException;
use app\common\services\common\CaptchaService;
use app\common\services\OperateLogsService;

/**
 * Class Login
 * @NoMiddleware("app\api\middleware\AuthMiddleware")
 */
class Login extends BaseController
{

    /**
     * 登录
     * @method(POST)
     */
    public function index(){

//        $data = $this->vali([
//            'username.require' => '用户不能为空!',
//            'password.require' => '登录密码不能为空!',
//            'password.min:4'   => '登录密码不能少于4位字符!',
//        ]);
//        $this->success('登录成功',MemberService::instance()->login($data['username'], $data['password'], 'member'));
//

        $username= $this->request->param('username');

        try {
            $data = $this->vali([
                'username.require' => '用户不能为空!',
                'password.require' => '登录密码不能为空!',
                'password.min:4'   => '登录密码不能少于4位字符!',
            ]);
            if(!empty($this->request->param("type")) && $this->request->param("type")=="web"){
                $captcha = $this->request->postMore([
                    ['captcha_code', ''],
                    ['captcha_uniqid', '']
                ]);
                if(!CaptchaService::instance()->check($captcha['captcha_code'], $captcha['captcha_uniqid'])) {
                    throw new CommonException('图形验证码错误，请重新输入',702);
                }
            }

            $login = app(MemberService::class)->login($username, $data['password'], 'member');
            throw new CommonException('',0);
        }catch (\Exception $e){
            $loginLogService = app(OperateLogsService::class);
            $memberId = $login['member']['id'] ?? 0;
            if(empty($memberId) && !empty($username)){
                $memberId = app(MemberService::class)->accountByToId($username);
            }
            if($e->getCode() === 0 && !empty($login)){
                $loginLogService->createLog($memberId, 'member','login','登录成功');
                $this->success($login);
            }else {
                $loginLogService->createLog($memberId, 'member','login','登录失败',"【{$e->getCode()}】:".$e->getMessage());
                $this->error($e->getMessage(),[],$e->getCode());
            }
        }
    }

    /**
     * 注册
     * @method(POST)
     */
    public function register(){

        $data = $this->request->postMore([
            ['mobile', ''],
            ['pwd',''],
            ['conf_pwd',''],
            ['invite_code', ''],
            ['code', ''],
            ["email",""],
            ["register_type",""]
        ]);
        validate(MemberValidate::class)->check($data);

//        $this->checkCode($data['mobile'],$data['code']);
        $memberServer = app(MemberService::class);
        if($memberServer->register($data)){
            $this->success('注册成功!');
        }
        $this->error($memberServer->getError()?:'注册失败!');
    }

}