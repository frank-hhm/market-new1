<?php
/**

 * @Date: 2024/3/5 15:04
 */
declare(strict_types=1);

namespace app\common\enum\order;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class SellTypeEnum
 * @package app\common\enum\order
 */
class SellTypeEnum extends BaseEnum
{
    // 用户手动
    const USER = 'user';
    // 触发保证金比例过低( <40 )
    const BOND = 'bond';

    // 触发保隔夜-保证金比例过低( <500 )
    const OVERNIGHT = 'overnight';

    // 触发止损
    const LOSS = 'loss';

    // 触发止盈
    const SURPLUS = 'surplus';

    // 平台
    const ADMIN = 'admin';

    // 触发
    const MARK = 'mark';

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::USER => [
                'name' => '用户',
                'value' => self::USER,
                'desc' => '用户手动'
            ],
            self::BOND => [
                'name' => '强平',
                'value' => self::BOND,
                'desc' => '触发保证金比例过低( <40 )'
            ],
            self::OVERNIGHT => [
                'name' => '隔夜强平',
                'value' => self::OVERNIGHT,
                'desc' => '触发保隔夜-保证金比例过低( <500 )'
            ],
            self::LOSS => [
                'name' => '触发止损',
                'value' => self::LOSS,
                'desc' => '价格触达止损线'
            ],
            self::SURPLUS => [
                'name' => '触发止盈',
                'value' => self::SURPLUS,
                'desc' => '价格触达止盈线'
            ],
            self::ADMIN => [
                'name' => '平台',
                'value' => self::ADMIN,
                'desc' => '后台平仓'
            ],
            self::MARK => [
                'name' => '触发标记',
                'value' => self::MARK,
                'desc' => '触发标记'
            ],
        ];
    }
}
