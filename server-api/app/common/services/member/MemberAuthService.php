<?php
/**
 * @Date: 2025/6/26 0:59
 */
declare(strict_types=1);
namespace app\common\services\member;

use app\common\constants\CacheKeyConstant;
use app\common\dao\member\MemberDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\services\member\MemberService;

use think\facade\Env;

/**
 * Class MemberAuthService
 */
class MemberAuthService extends BaseService
{

    /**
     * MemberAuthService constructor.
     * @param MemberDao $dao
     */
    public function __construct(MemberDao $dao)
    {
        $this->dao = $dao;
    }

    public function isLogin($token){

        $authInfo = $this->parseToken($token);
        if(!empty($authInfo['member']['id'])){
            return $authInfo['member'];
        }
        return false;
    }

    /**
     * 获取授权信息
     */
    public function parseToken($token,$type = 'member'): array
    {
        $md5Token = is_null($token) ? '' : Env::get('redis.jwt').':'.$type.':'.md5($token);
        if ($token === 'undefined') {
            throw new CommonException('请登录', 410000);
        }
        $cacheService =  app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
        if (!$token || !$tokenData = $cacheService->get($md5Token)){
            throw new CommonException('请登录', 410000);
        }

        if (!is_array($tokenData) || empty($tokenData) || !isset($tokenData['uid'])) {
            throw new CommonException('请登录', 410000);
        }

        $jwtService = app(JwtAuthService::class);
        //设置解析token
        [$id, $type] = $jwtService->parseToken($token);

        try {
            $jwtService->verifyToken();
        } catch (\Throwable $e) {
            if (!request()->isCli()) $cacheService->delete($md5Token);
            throw new CommonException('登录已过期,请重新登录', 410000);
        }
        $member = app(MemberService::class)->getCacheDetail($id);
        if (!$member || $member['id'] != $tokenData['uid']) {
            if (!request()->isCli()) $cacheService->delete($md5Token);
            throw new CommonException('登录状态有误,请重新登录', 410000);
        }
        $tokenData['type'] = $type;
        return compact('member', 'tokenData');
    }

}