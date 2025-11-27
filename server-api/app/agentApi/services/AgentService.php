<?php
/**
 * @Date: 2025/7/5 21:39
 */
declare(strict_types=1);

namespace app\agentApi\services;

use app\common\constants\CacheKeyConstant;
use app\common\services\common\CacheService;
use app\common\services\common\JwtAuthService;
use app\common\constants\StatusCode;
use app\common\exception\CommonException;
use Firebase\JWT\ExpiredException;
use think\facade\Env;
use app\common\services\agent\AgentService as CommonService;

/**
 *
 * Class AgentService
 * @package app\agentApi\services
 */
class AgentService extends CommonService
{


    /**
     * 修改密码
     */
    public function updatePass(array $data): bool
    {
        $adminId = request()->agentId();
        $detail = $this->getDetail(request()->agentId());
        if (!$detail) {
            throw new CommonException('账号不存在!');
        }
        if (!password_verify($data['old_pwd'], $detail['pwd'])) {
            throw new CommonException('原密码错误，请重新输入');
        }

        //修改密码
        $pwd = $this->dao->passwordHash($data['pwd']);
        if ($this->dao->update($adminId,['pwd'=>$pwd])) {
            return true;
        } else {
            return false;
        }
    }

}