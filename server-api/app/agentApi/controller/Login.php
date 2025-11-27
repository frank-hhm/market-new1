<?php
/**
 * @Date: 2025/7/5 21:10
 */
declare(strict_types=1);
namespace app\agentApi\controller;
use app\agentApi\services\AgentAuthService;
use app\BaseController;
use app\common\constants\CacheKeyConstant;
use app\common\exception\CommonException;
use app\common\services\common\CacheService;
use app\common\services\common\CaptchaService;
use app\common\services\OperateLogsService;
use app\common\services\system\LoginLogsService;
use think\facade\Log;
use think\facade\Queue;

/**
 * 登陆
 * @NoMiddleware("app\agentApi\middleware\AuthMiddleware")
 */
class Login extends BaseController
{
    /**
     * 登陆授权
     * @method(POST)
     */
    public function index()
    {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? $this->request->ip();
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
        $cacheKey = 'agent:login:'.$ip;
        $cacheCount = $cacheService->get($cacheKey);
        empty($cacheCount) && $cacheCount = 0;
        $cacheCount++;
        $cacheService->set($cacheKey, $cacheCount,15 * 60);
        if($cacheCount > 10){
//            throw new CommonException('您登录次数过多，请稍后再试',703);
        }
        $account = $this->request->param('account');
        try {
            $data = $this->vali([
                'account.require' => '账号不能为空!',
                'account.min:3'   => '账号不能少于3位字符!',
                'password.require' => '登录密码不能为空!',
                'password.min:4'   => '登录密码不能少于4位字符!',
                'captcha_code.require'   => '图形验证码不能为空!',
                'captcha_uniqid.require'   => '图形验证标识不能为空!',
            ]);
            if(!CaptchaService::instance()->check($data['captcha_code'], $data['captcha_uniqid'])) {
                throw new CommonException('验证码错误，请重新输入',702);
            }

            $login = app(AgentAuthService::class)->login($account, $data['password'], 'agent');
            throw new CommonException('',0);
        }catch (\Exception $e){
            $loginLogService = app(OperateLogsService::class);
            $agentId = $login['user_info']['id'] ?? 0;
            if(empty($agentId) && !empty($account)){
                $agentId = app(AgentAuthService::class)->accountByToId($account);
            }
            if($e->getCode() === 0 && !empty($login)){
                $loginLogService->createLog($agentId, 'agent','login','登录成功');
                $this->success($login);
            }else {
                $loginLogService->createLog($agentId, 'agent','login','登录失败',"【{$e->getCode()}】:".$e->getMessage());
                $this->error($e->getMessage(),[],$e->getCode());
            }
        }
    }
}