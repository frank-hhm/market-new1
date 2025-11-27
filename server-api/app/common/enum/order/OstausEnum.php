<?php
/**

 * @Date: 2024/3/5 14:37
 */
declare(strict_types=1);

namespace app\common\enum\order;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OstausEnum
 * @package app\common\enum\order
 */
class OstausEnum extends BaseEnum
{
    // 建仓
    const A = 0;

    // 平仓
    const B = 1;

    // 委托
    const C = 2;

    // 撤单
    const D = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '建仓',
                'name2' => '买入',
                'value' => self::A,
            ],
            self::B => [
                'name' => '平仓',
                'name2' => '卖出',
                'value' => self::B,
            ],
            self::C => [
                'name' => '委托',
                'value' => self::C,
            ],
            self::D => [
                'name' => '撤单',
                'value' => self::D,
            ],
        ];
    }
}
