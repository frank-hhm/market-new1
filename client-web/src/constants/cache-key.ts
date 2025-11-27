let PREFIX = "chient-web"

if (process.env.NODE_ENV === 'development') {
    PREFIX = 'dev-' + PREFIX
}
/** 缓存数据时用到的 Key */
class CacheKey {
    static TOKEN = `${PREFIX}-token-key`
    static LAYOUT = `${PREFIX}-layout`
    static TEMPLATE_DARK = `${PREFIX}-template-dark`
    
    static ACTIVE_THEME_NAME = `${PREFIX}-active-theme-name-key`
    static CONFIG = `${PREFIX}-config-key`
    static MEMBER_DEFAULT_COLOR = `${PREFIX}-member-default-color`
    static SELECT_MARKET_ID = `${PREFIX}-select-market-id`
    static PAGE_LIMIT = `${PREFIX}-page-limit-key`
    static LOGINDATA = `${PREFIX}-login-data`
    static MAININDICATORSINDEX = `${PREFIX}-main-indicators-index`
    static ASSISTANTINDICATORSINDEX = `${PREFIX}-assistant-indicators-index`
    
}

export default CacheKey
