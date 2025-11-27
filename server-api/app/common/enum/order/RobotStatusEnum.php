<?php
/**
 * @Date: 2025/6/30 21:05
 */
declare(strict_types=1);

namespace app\common\enum\order;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class RobotStatusEnum
 * @package app\common\enum\order
 */
class RobotStatusEnum extends BaseEnum
{
    // 挂单中
    const A = 0;

    // 已成交
    const B = 1;

    // 撤单
    const C = 2;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '挂单中',
                'value' => self::A,
            ],
            self::B => [
                'name' => '已成交',
                'value' => self::B,
            ],
            self::C => [
                'name' => '撤单',
                'value' => self::C,
            ],
        ];
    }
}
