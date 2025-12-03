<?php
/**
 * @Date: 2025/6/21 7:34
 */
declare(strict_types=1);
namespace app\common\constants;

/**
 * @Constants
 */
class CacheKeyConstant
{
    //JWT redis驱动名称
    public const JWT_REDIS_DRIVER = 'redis_jwt';
    //用户 redis驱动名称
    public const MEMBER_REDIS_DRIVER = 'redis_member';
    //产品redis驱动名称
    public const QUEUE_REDIS_DRIVER = 'redis_queue';
    //产品redis驱动名称
    public const PRODUCT_REDIS_DRIVER = 'redis';
    //产品redis驱动名称
    public const PRODUCT_MARKET_REDIS_DRIVER = 'redis_market';
    // 自主行情生成器状态
    public const MARKET_AUTO_STATUS = 'MarketAutoStatus';
    // itick-forex行情生成器状态
    public const MARKET_ITICK_FOREX_STATUS = 'MarketItickForexStatus';
    // itick-forex行情生成器状态
    public const MARKET_ITICK_FOREX_AXU_STATUS = 'MarketItickForexAXUStatus';
    public const MARKET_ITICK_FOREX_XAG_STATUS = 'MarketItickForexXAGStatus';
    public const MARKET_ITICK_FUTURE_STATUS = 'MarketItickFutureStatus';

    // Itick-crypto 币种状态
    public const MARKET_ITICK_CRYPTO_STATUS = 'MarketItickCryptoStatus';

    // itick-Indices行情生成器状态
    public const MARKET_ITICK_INDICES_STATUS = 'MarketItickIndicesStatus';
    // 产品列表
    const PRODUCT_SELECT = 'product:select';

    // 产品详情
    const PRODUCT_DETAIL = 'product:detail';
    // 产品详情
    const PRODUCT_PARAMS = 'product:params';
    // 产品价格调控
    const PRODUCT_SET_PRICE_PIN = 'product:setPricePin';
    const PRODUCT_SET_PRICE_HOU = 'product:setPriceHou';

    // 产品价格
    const PRODUCT_PRICE = 'product:price';

    // 产品k线
    const PRODUCT_K_LINE = 'product:kline';
    // 产品板块
    const PRODUCT_SECTOR = 'product:sector';

    // 产品调控
    const PRODUCT_CONTROL = 'product:control';

    //Api提交重复检测
    const API_SUBMIT_LOCK = 'api:submit:lock';

}