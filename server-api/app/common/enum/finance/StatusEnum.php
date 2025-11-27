<?php
/**
 * @Date: 2025/6/26 0:57
 */
declare(strict_types=1);
namespace app\common\enum\finance;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class StatusEnum
 * @package app\common\enum\agent
 */
class StatusEnum extends BaseEnum
{

    // 待审核
    const A = 0;

    // 审核通过
    const B = 1;

    // 驳回
    const C = 2;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '待审核',
                'value' => self::A,
                'color' => 'grey',
            ],
            self::B => [
                'name' => '审核通过',
                'value' => self::B,
                'color' => 'green',
            ],
            self::C => [
                'name' => '驳回',
                'value' => self::C,
                'color' => 'red',
            ],
        ];
    }
}