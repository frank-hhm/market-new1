<?php
/**
 * @Date: 2025/6/26 0:57
 */
declare(strict_types=1);

namespace app\common\enum\finance;

use app\common\enum\BaseEnum;

/**
 * 枚举类
 * Class RechargePayTypeEnum
 * @package app\common\enum\finance
 */
class RechargePayTypeEnum extends BaseEnum
{
    // 抽奖奖励
    const TURNTABLE = 'turntable';
    // 线下支付宝充值
    const OFFLINE_ALIPAY = 'offline_alipay';
    // 线上支付宝充值
    const ALIPAY = 'alipay';

    // 线下银行卡充值
    const OFFLINE_BANK = 'offline_bank';

    // 线下usdt充值
    const OFFLINE_USDT = 'offline_usdt';

    // 支付宝转账
    const ALIPAY_TRANSFER = 'alipay_transfer';
    // 微信扫码
    const WECHAT_QRCODE = 'wechat_qrcode';

    // 账户
    const BALANCE = 'balance';
    // 佣金账户
    const COMMISSION_BALANCE = 'commission_balance';

    // 跟单余额
    const FOLLOW_BALANCE = 'follow';
    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::OFFLINE_ALIPAY => [
                'name' => '线下支付宝',
                'value' => self::OFFLINE_ALIPAY,
            ],
            self::ALIPAY => [
                'name' => '线上支付宝',
                'value' => self::ALIPAY,
            ],
            self::OFFLINE_BANK => [
                'name' => '线下银行卡',
                'value' => self::OFFLINE_BANK,
            ],
            self::OFFLINE_USDT => [
                'name' => '线下usdt',
                'value' => self::OFFLINE_USDT,
            ],
            self::ALIPAY_TRANSFER => [
                'name' => '支付宝转账',
                'value' => self::ALIPAY_TRANSFER,
            ],
            self::WECHAT_QRCODE => [
                'name' => '微信扫码',
                'value' => self::WECHAT_QRCODE,
            ],
            self::TURNTABLE => [
                'name' => '抽奖奖励',
                'value' => self::TURNTABLE,
            ],
            self::BALANCE => [
                'name' => '余额',
                'value' => self::BALANCE,
            ],
            self::COMMISSION_BALANCE => [
                'name' => '佣金余额',
                'value' => self::COMMISSION_BALANCE,
            ],
            self::FOLLOW_BALANCE => [
                'name' => '佣金余额',
                'value' => self::FOLLOW_BALANCE,
            ],

        ];
    }
}
