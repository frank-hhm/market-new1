<?php
/**
 * @Date: 2025/7/5 21:13
 */
declare(strict_types=1);

namespace app\agentApi\services;

use app\common\constants\CacheKeyConstant;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\constants\StatusCode;
use app\common\exception\CommonException;
use app\common\services\system\MenusService;
use Firebase\JWT\ExpiredException;
use think\facade\Env;
use app\common\services\agent\AgentService as CommonService;

/**
 * 授权service
 * Class AgentAuthService
 * @package app\agentApi\services
 */
class AgentAuthService extends CommonService
{

    // 登陆获取菜单获取token
    public function login(string $account, string $password, string $type = 'agent'): array
    {
        $agentDetail = $this->verifyLogin($account, $password);

        $tokenInfo = app(JwtAuthService::class)->createToken($agentDetail->id, $type);

        $menus = app(MenusService::class)->getAgentMenusList();
        $exp =  $tokenInfo['params']['exp'];
        return [
            'token' => $tokenInfo['token'],
            'expires_time' => $exp,
            'menus' => $menus,
            'agent_info' => $agentDetail->hidden(['pwd'])
        ];
    }
    // 账号登陆
    public function verifyLogin(string $account, string $password)
    {
        $agent = $this->dao->getDetailByAccount($account);
        if (!$agent) {
            throw new CommonException('账号不存在!');
        }
        if (!$agent->status) {
            throw new CommonException('您已被禁止登录!');
        }

        if (!password_verify($password, $agent->pwd)) {
            throw new CommonException('账号或密码错误，请重新输入',701);
        }
        $agent->last_time = time();
        $agent->last_ip = app('request')->ip();
        $agent->login_count++;
        $agent->save();
        return $agent;
    }
    /**
     * 获取代理商授权信息
     */
    public function parseToken(string $token,string $type = 'agent'): array
    {
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
        if (!$token || $token === 'undefined') {
            throw new CommonException(StatusCode::ERR_LOGIN);
        }
        //设置解析token
        [$id, $type] = JwtAuthService::instance()->parseToken($token);
        //检测token是否过期
        $md5Token = Env::get('redis.jwt').':'.$type.':'.md5($token);
        if (!$cacheService->has($md5Token) || !($cacheToken = $cacheService->get($md5Token))) {
            $this->authFailAfter($id, $type);
            throw new CommonException(StatusCode::ERR_LOGIN);
        }
        //是否超出有效次数
        if (isset($cacheToken['invalidNum']) && $cacheToken['invalidNum'] >= 3) {
            if (!request()->isCli()) {
                $cacheService->clear($md5Token);
            }
            $this->authFailAfter($id, $type);
            throw new CommonException(StatusCode::ERR_LOGIN);
        }


        //验证token
        try {
            JwtAuthService::instance()->verifyToken();
            $cacheService->set($md5Token, $cacheToken, $cacheToken['exp']);
        } catch (ExpiredException $e) {
            $cacheToken['invalidNum'] = isset($cacheToken['invalidNum']) ? $cacheToken['invalidNum']++ : 1;
            $cacheService->set($md5Token, $cacheToken, $cacheToken['exp']);
        } catch (\Exception $e) {
            if (!request()->isCli()) {
                $cacheService->clear($md5Token);
            }
            $this->authFailAfter($id, $type);
            throw new CommonException(StatusCode::ERR_LOGIN);
        }

        //获取信息
        $agentDetail = $this->dao->model->where('id',$id)->find();

        if (!$agentDetail || !$agentDetail->id) {
            if (!request()->isCli()) {
                $cacheService->clear($md5Token);
            }
            $this->authFailAfter($id, $type);
            throw new CommonException(StatusCode::ERR_LOGIN);
        }

        $agentDetail->type = $type;
        $res['info'] = $agentDetail->toArray();
        return $res;

    }
    /**
     * token验证失败后事件
     */
    protected function authFailAfter($id, $type): void
    {
    }
}