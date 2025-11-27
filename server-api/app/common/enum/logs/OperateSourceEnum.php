<?php
/**
 * @Date: 2025/7/6 20:41
 */
declare(strict_types=1);

namespace app\common\enum\logs;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OperateSourceEnum
 * @package app\common\enum\logs
 */
class OperateSourceEnum extends BaseEnum
{
    // 用户
    const MEMBER = 'member';

    // 后台
    const ADMIN = 'admin';

    // 代理
    const AGENT = 'agent';

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::MEMBER => [
                'name' => '用户',
                'value' => self::MEMBER
            ],
            self::ADMIN => [
                'name' => '后台',
                'value' => self::ADMIN
            ],
            self::AGENT => [
                'name' => '代理',
                'value' => self::AGENT
            ],
        ];
    }
}
