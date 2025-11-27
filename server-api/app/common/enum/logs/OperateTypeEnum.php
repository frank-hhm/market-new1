<?php
/**
 * @Date: 2025/7/6 20:44
 */
declare(strict_types=1);

namespace app\common\enum\logs;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OperateTypeEnum
 * @package app\common\enum\logs
 */
class OperateTypeEnum extends BaseEnum
{
    // 登录
    const LOGIN = 'login';

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::LOGIN => [
                'name' => '登录',
                'value' => self::LOGIN
            ],
        ];
    }
}
