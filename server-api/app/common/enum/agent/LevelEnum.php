<?php
/**
 * @Date: 2025/6/26 0:58
 */
declare(strict_types=1);

namespace app\common\enum\agent;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class LevelEnum
 * @package app\common\enum\agent
 */
class LevelEnum extends BaseEnum
{
    // 一级代理
    const ONE = 1;

    // 二级代理
    const TWO = 2;

    // 三级代理
    const THREE = 3;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::ONE => [
                'name' => '一级代理',
                'value' => self::ONE,
            ],
            self::TWO => [
                'name' => '二级代理',
                'value' => self::TWO,
            ],
            self::THREE => [
                'name' => '三级代理',
                'value' => self::THREE,
            ],
        ];
    }
}
