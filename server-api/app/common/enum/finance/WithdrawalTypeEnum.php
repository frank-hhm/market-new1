<?php
/**
 * @Date: 2025/6/26 0:58
 */
declare(strict_types=1);
namespace app\common\enum\finance;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class WithdrawalTypeEnum
 * @package app\common\enum\finance
 */
class WithdrawalTypeEnum extends BaseEnum
{
    // 账户余额
    const BALANCE = 'balance';
    // 佣金余额
    const COMMISSION = 'commission_balance';
    // 跟单余额
    const FOLLOW = 'follow';


    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::BALANCE => [
                'name' => '账户余额',
                'value' => self::BALANCE,
            ],
            self::COMMISSION => [
                'name' => '佣金余额',
                'value' => self::COMMISSION,
            ],
            self::FOLLOW => [
                'name' => '跟单余额',
                'value' => self::FOLLOW,
            ],
        ];
    }
}
