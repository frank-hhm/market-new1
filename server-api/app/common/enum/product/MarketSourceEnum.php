<?php
/**
 * @Date: 2025/6/11 12:37
 */
declare(strict_types=1);

namespace app\common\enum\product;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class MarketSourceEnum
 * @package app\common\enum\product
 */
class MarketSourceEnum extends BaseEnum
{
    // 自主生成
    const AUTO = 'auto';

    // 数海
    const SHUHAI = 'shuhai';

    // itick-stock
    const ITICK_STOCK = 'itick_stock';

    // itick-forex
    const ITICK_FOREX = 'itick_forex';

    const ITICK_FOREX_AXU = 'itick_forex_axu';

    const ITICK_FOREX_AXG = 'itick_forex_axg';

    // itick-indices
    const ITICK_INDICES = 'itick_indices';
    // itick-crypto
    const ITICK_CRYPTO = 'itick_crypto';

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::AUTO => [
                'name' => '自主生成',
                'value' => self::AUTO,
            ],
//            self::SHUHAI => [
//                'name' => '数海',
//                'value' => self::SHUHAI,
//            ],
            self::ITICK_FOREX => [
                'name' => 'ITICK-外汇',
                'value' => self::ITICK_FOREX,
            ],
            self::ITICK_INDICES => [
                'name' => 'ITICK-指数',
                'value' => self::ITICK_INDICES,
            ],
            self::ITICK_CRYPTO => [
                'name' => 'ITICK-加密货币',
                'value' => self::ITICK_CRYPTO,
            ],
            self::ITICK_FOREX_AXU => [
                'name' => 'ITICK-黄金',
                'value' => self::ITICK_FOREX_AXU,
            ],
            self::ITICK_FOREX_AXG => [
                'name' => 'ITICK-白银',
                'value' => self::ITICK_FOREX_AXG,
            ],

        ];
    }
}
