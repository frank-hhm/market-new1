<?php
/**
 * @Date: 2025/7/6 20:41
 */
declare(strict_types=1);

namespace app\common\enum\follow;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OrderStatusEnum
 * @package app\common\enum\logs
 */
class OrderStatusEnum extends BaseEnum
{
    // 禁用
    const A = 0;

    // 正常
    const B = 1;

    // 结束
    const C = 2;


    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '禁用',
                'value' => self::A,
                "color"=>"red",
                'name2' => '跟单结束'
            ],
            self::B => [
                'name' => '进行中',
                'value' => self::B,
                "color"=>"green",
                'name2' => '跟单中',
            ],
            self::C => [
                'name' => '已结束',
                'value' => self::C,
                "color"=>"grey",
                'name2' => '跟单结束'
            ],
        ];
    }
}
