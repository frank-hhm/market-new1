<?php
/**
 * @Date: 2025/6/26 0:49
 */
declare(strict_types=1);
namespace app\common\enum\member;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class BindStatusEnum
 * @package app\common\enum\agent
 */
class BindStatusEnum extends BaseEnum
{

    // 未认证
    const A = 0;

    // 认证成功
    const B = 1;

    // 审核中
    const C = 2;

    // 认证失败
    const D = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::A => [
                'name' => '未认证',
                'value' => self::A,
                'color' => 'grey',
            ],
            self::B => [
                'name' => '认证成功',
                'value' => self::B,
                'color' => 'green',
            ],
            self::C => [
                'name' => '审核中',
                'value' => self::C,
                'color' => 'blue',
            ],
            self::D => [
                'name' => '认证失败',
                'value' => self::D,
                'color' => 'red',
            ],
        ];
    }
}
