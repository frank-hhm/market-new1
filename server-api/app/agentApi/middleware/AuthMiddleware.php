<?php
/**
 * @Date: 2025/7/6 11:26
 */
declare(strict_types=1);
namespace app\agentApi\middleware;


use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\middleware\MiddlewareInterface;
use app\agentApi\services\AgentAuthService;
use app\Request;
/**
 * Class AuthMiddleware
 * @package app\agentApi\middleware
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next)
    {
        $annotate = $request->annotate();
        $force = $annotate['force'][0] ?? true;
        $authInfo = null;
        $authori = $request->header('Authori-zation') ?? '';
        $token = trim(ltrim($authori, 'Bearer'));
        try {
            $authInfo = app(AgentAuthService::class)->parseToken($token,'agent');
        } catch (\Exception $e) {
            if (StringHelper::isTrue($force))
                throw new CommonException($e->getMessage(),$e->getCode());
        }
        if (!is_null($authInfo)) {
            Request::macro('agent', function (string $key = null) use (&$authInfo) {
                if ($key) {
                    return $authInfo[$key] ?? '';
                }
                return $authInfo;
            });
            Request::macro('tokenData', function () use (&$authInfo) {
                return $authInfo['tokenData'];
            });
        }
        Request::macro('isLogin', function () use (&$authInfo) {
            return !is_null($authInfo);
        });
        Request::macro('agentId', function () use (&$authInfo) {
            return is_null($authInfo) ? 0 : (int)$authInfo['info']['id'];
        });
        return $next($request);
    }
}