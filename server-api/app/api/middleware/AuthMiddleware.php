<?php
/**
 * @Date: 2025/6/26 1:21
 */
declare(strict_types=1);
namespace app\api\middleware;


use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\middleware\MiddlewareInterface;
use app\common\services\member\MemberAuthService;
use app\Request;
use think\facade\Config;
/**
 * Class AuthMiddleware
 * @package app\api\middleware
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
            $authInfo = MemberAuthService::instance()->parseToken($token,'member');
        } catch (\Exception $e) {
            if (StringHelper::isTrue($force))
                throw new CommonException($e->getMessage(),$e->getCode());
        }
        if (!is_null($authInfo)) {
            Request::macro('member', function (string $key = null) use (&$authInfo) {
                if ($key) {
                    return $authInfo['member'][$key] ?? '';
                }
                return $authInfo['member'];
            });
            Request::macro('tokenData', function () use (&$authInfo) {
                return $authInfo['tokenData'];
            });
        }
        Request::macro('isLogin', function () use (&$authInfo) {
            return !is_null($authInfo);
        });
        Request::macro('uid', function () use (&$authInfo) {
            return is_null($authInfo) ? 0 : (int)$authInfo['member']['id'];
        });
        return $next($request);
    }
}