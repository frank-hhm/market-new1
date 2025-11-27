<?php
/**

 * @Date: 2024/3/5 15:05
 */
declare(strict_types=1);

namespace app\common\enum\order;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OstyleEnum
 * @package app\common\enum\order
 */
class OstyleEnum extends BaseEnum
{
    // 买跌
    const A = 1;

    // 买涨
    const B = 2;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '买跌',
                'value' => self::A,
                'color' => 'green',
            ],
            self::B => [
                'name' => '买涨',
                'value' => self::B,
                'color' => 'red',
            ],
        ];
    }
}
