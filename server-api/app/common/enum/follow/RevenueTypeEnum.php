<?php
/**
 * @Date: 2025/7/6 20:41
 */
declare(strict_types=1);

namespace app\common\enum\follow;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class OperateSourceEnum
 * @package app\common\enum\logs
 */
class RevenueTypeEnum extends BaseEnum
{
    // 随机生成
    const AUTO = 'auto';

    // 固定
    const LOCK = 'lock';


    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::AUTO => [
                'name' => '大小之间随机%比例',
                'value' => self::AUTO
            ],
            self::LOCK => [
                'name' => '固定比例',
                'value' => self::LOCK
            ],
        ];
    }
}
