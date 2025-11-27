<?php
/**

 * @Date: 2024/3/5 15:04
 */
declare(strict_types=1);

namespace app\common\enum\order;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class SysPingEnum
 * @package app\common\enum\order
 */
class SysPingEnum extends BaseEnum
{
    // 用户平仓
    const A = 0;

    // 系统平仓
    const B = 1;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '用户平仓',
                'value' => self::A,
            ],
            self::B => [
                'name' => '系统平仓',
                'value' => self::B,
            ],
        ];
    }
}
