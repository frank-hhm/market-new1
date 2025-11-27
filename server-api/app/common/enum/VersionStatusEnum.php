<?php
/**
 * @Date: 2025/7/7 19:57
 */
declare(strict_types=1);

namespace app\common\enum;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class StatusEnum
 * @package app\common\enum
 */
class VersionStatusEnum extends BaseEnum
{
    // 待更新
    const A = 0;
    const B = 1;
    const E = 11;
    const C = 2;
    const CS = 22;

    const D = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '待执行',
                'value' => self::A,
            ],
            self::B => [
                'name' => '当前执行成功，待推送',
                'value' => self::B,
            ],
            self::E => [
                'name' => '当前执行失败',
                'value' => self::B,
            ],
            self::C => [
                'name' => '推送完成',
                'value' => self::C,
            ],
            self::CS => [
                'name' => '更新完成',
                'value' => self::CS,
            ],
            self::D => [
                'name' => '推送失败',
                'value' => self::D,
            ],
        ];
    }
}
