<?php
/**
 * @Date: 2025/6/26 0:57
 */
declare(strict_types=1);
namespace app\common\enum\finance;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class WaterTypeEnum
 * @package app\common\enum\agent
 */
class WaterTypeEnum extends BaseEnum
{

    // 减少
    const A = 0;

    // 增加
    const B = 1;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '减少',
                'value' => self::A,
                'color' => 'red',
                'name2' => '亏损',
            ],
            self::B => [
                'name' => '增加',
                'value' => self::B,
                'color' => 'green',
                'name2' => '收益',
            ],
        ];
    }
}