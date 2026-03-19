<?php
/**
 * @Date: 2025/6/26 0:57
 */
declare(strict_types=1);

namespace app\common\enum\finance;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class SourceEnum
 * @package app\common\enum\finance
 */
class SourceEnum extends BaseEnum
{
    // 用户充值
    const RECHARGE = 'recharge';

    // 后台充值
    const ADMIN = 'admin';

    // 平仓
    const SETTLEMENT = 'settlement';

    // 佣金
    const COMMISSION = 'commission';

    // 手续费
    const FEE = 'fee';

    // 代理商提现
    const AGENT_WITHDRAWAL = 'agent_withdrawal';

    // 用户提现
    const MEMBER_WITHDRAWAL = 'member_withdrawal';

    // 活动
    const ACTIVITY = 'activity';

    // 手续费佣金
    const COMMISSION_FEE = 'commission_fee';
    // 佣金提现
    const MEMBER_COMMISSION_WITHDRAWAL = 'member_commission_withdrawal';

    // 手续费返现
    const FEE_CASH = 'fee_cash';
    // 跟单
    const FOLLOW = 'follow';
    // 跟单收益
    const FOLLOW_REVENUE = 'follow_revenue';
    // 跟单收益分佣
    const FOLLOW_REVENUE_COMMISSION = 'follow_revenue_commission';
    // 跟单收益分佣2
    const FOLLOW_REVENUE_COMMISSION2 = 'follow_revenue_commission2';
    // 跟单结束返还
    const FOLLOW_CLOSE = 'follow_close';
    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::RECHARGE => [
                'name' => '用户充值',
                'value' => self::RECHARGE,
                'describe' => '用户充值',
                'name2' => '存款',
                'name2_color' => 'red',
            ],
            self::ADMIN => [
                'name' => '系统充值',
                'value' => self::ADMIN,
                'describe' => '系统充值 [%s] 操作[%s]',
            ],
            self::SETTLEMENT => [
                'name' => '平仓',
                'value' => self::SETTLEMENT,
                'describe' => '平仓',
            ],
            self::COMMISSION => [
                'name' => '佣金',
                'value' => self::COMMISSION,
                'describe' => '佣金',
                'name2' => '合约佣金',
                'name2_color' => 'red',
            ],
            self::FEE => [
                'name' => '手续费',
                'value' => self::FEE,
                'describe' => '手续费',
            ],
            self::AGENT_WITHDRAWAL => [
                'name' => '代理商提现',
                'value' => self::AGENT_WITHDRAWAL,
                'describe' => '代理商提现',
            ],
            self::MEMBER_WITHDRAWAL => [
                'name' => '用户提现',
                'value' => self::MEMBER_WITHDRAWAL,
                'describe' => '用户提现',
                'name2' => '取款',
                'name2_color' => 'green',
            ],
            self::ACTIVITY => [
                'name' => '活动赠金',
                'value' => self::ACTIVITY,
                'describe' => '活动赠金',
            ],
            self::COMMISSION_FEE => [
                'name' => '佣金',
                'value' => self::COMMISSION_FEE,
                'describe' => '佣金',
                'name2' => '合约佣金',
                'name2_color' => 'green',
            ],
            self::MEMBER_COMMISSION_WITHDRAWAL => [
                'name' => '佣金提现',
                'value' => self::MEMBER_COMMISSION_WITHDRAWAL,
                'describe' => '佣金提现',
                'name2' => '佣金提现',
                'name2_color' => 'green',
            ],
            self::FEE_CASH => [
                'name' => '手续费返现',
                'value' => self::FEE_CASH,
                'describe' => '手续费返现',
            ],
            self::FOLLOW => [
                'name' => '跟单',
                'value' => self::FOLLOW,
                'describe' => '跟单',
                'name2' => '跟单',
            ],
            self::FOLLOW_REVENUE => [
                'name' => '跟单收益',
                'value' => self::FOLLOW_REVENUE,
                'describe' => '跟单收益',
                'name2' => '跟单',
            ],
            self::FOLLOW_REVENUE_COMMISSION => [
                'name' => '跟单收益分佣1级',
                'value' => self::FOLLOW_REVENUE_COMMISSION,
                'describe' => '跟单收益分佣',
                'name2' => '跟单收益分佣1级',
            ],
            self::FOLLOW_REVENUE_COMMISSION2 => [
                'name' => '跟单收益分佣2级',
                'value' => self::FOLLOW_REVENUE_COMMISSION2,
                'describe' => '跟单收益分佣',
                'name2' => '跟单收益分佣2级',
            ],
            self::FOLLOW_CLOSE => [
                'name' => '跟单结束返还',
                'value' => self::FOLLOW_CLOSE,
                'describe' => '跟单结束返还',
                'name2' => '跟单结束返还',
            ],

        ];
    }
}
