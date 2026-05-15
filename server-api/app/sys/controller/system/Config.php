<?php
/**
 * @Date: 2023/12/16 13:32
 */
declare(strict_types=1);

namespace app\sys\controller\system;

use app\common\constants\CacheKeyConstant;
use app\common\helper\ArrayHelper;
use app\common\library\api\PublishVersionService;
use app\common\library\publish\VersionService;
use app\common\services\common\CacheService;
use app\common\services\VersionDataService;
use app\sys\controller\Base;
use think\facade\Db;
use think\facade\Env;
use think\facade\Log;

/**
 * 配置
 */
class Config extends Base
{
    /**
     * 获取配置
     * @noAuth(true)
     * @method(GET)
     */
    public function get(){
        $type = $this->request->param('type');
        $keyArr = [
            'default' => [
                'system_name',
                'system_version',
                'system_logo',
                'system_icon',
                'web_domain',
                "copyright",
            ],
            'email' => [
                'mail_imap_host',
                'mail_imap_port',
                'mail_username',
                'mail_password',
                'mail_default_from',
                'mail_default_from_name',
            ],
            'trade'=>[
                "order_ping_time",
                "usdt_rate",
                "usdt_out_rate",
                "payment_usdt",
                'order_min_price',
                'order_max_price',
                'payment_alipays_status',
                'payment_alipay_appid',
                'payment_alipay_public_key',
                'payment_alipay_private_key',
                'payment_alipay_app_cert',
                'payment_alipay_root_cert',
                'member_recharge_message_email',
            ],
            'other'=>[
                "kefu_wechat",
                'kefu_qq',
                'kefu_other',
                'about_bg',
                'about_content',
                'about_content_know',
                'about_role',
                'about_product',
                'agreement_customer',
                'agreement_risk',
                'agreement_disclaimers',
                'login_banner',
                'moni_activity_time_tips',
                "ios_download_url",
                "android_download_url",
                "about_content_follow"
            ],
            'market'=>[
                'cnshuhai_username',
                'cnshuhai_password',
                'cnshuhai_subscribe',
                'itick_key'
            ],
            'agent'=>[
                'agent_withdrawal_rate',
                'member_withdrawal_rate',
                'member_usdt_withdrawal_rate',
                'member_usdt_withdrawal_money',
                'member_usdt_withdrawal_status',

                "member_alipay_withdrawal_status"
            ],
            "member"=>[
                "member_default_product"
            ],
        ];

        $addonArr = event("AddonSystemConfig");

        $keyArr = array_merge($keyArr,$addonArr);
//        dump($keyArr);die;

        $source = explode('.', $type);
        $keyArr2 = [];
        foreach ($source as $item) {
            if (isset($keyArr[$item])) {
                $keyArr2 = $keyArr[$item];
            }
        }
        $data = [];
        foreach ($keyArr2 as $key) {
            $value = sysconf($key);
            $data[$key] = is_null(json_decode($value)) ? $value : json_decode($value, true);
        }
        $this->success($data);
    }
    /**
     * 保存配置
     * @method(POST)
     */
    public function save(){
        $data = $this->request->post();
        foreach ($data as $key => $value) {
            $confValue = $value;
            if(is_array($value)){
                $confValue = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            sysconf($key, $confValue);
        }
        app(CacheService::class)->delete('system_config');
        $this->success('保存成功！');
    }
    /**
     * 清理缓存
     * @method(PUT)
     */
    public function cleanCache()
    {
        $data = $this->request->postMore([
            ['type',''],
        ]);
        if (empty($data['type'])){
            $this->error('请选择清理类型！');
        }
        $cacheTitle = "";
        $cacheData = [];
        switch ($data['type']){
            case 'user_login':
//                app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER)->set(Env::get('redis.jwt').':'.$type.':'.md5($tokenInfo['token']), ['uid' => $id, 'type' => $type, 'token' => $tokenInfo['token'], 'exp' => $exp], (int)$exp, $type);
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
                $cacheData = [[
                    'cache'=>$cacheService,
                    'cache_key'=>Env::get('redis.jwt').':member:*',
                ]];
                $cacheTitle = '用户登录缓存';
                break;
            case 'user':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::MEMBER_REDIS_DRIVER);
                $cacheData = [
                    [
                        'cache'=>$cacheService,
                        'cache_key'=>'member:*'
                    ]
                ];
                $cacheTitle = "用户数据缓存";
                break;
            case 'admin_login':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
                $cacheData = [[
                    'cache'=>$cacheService,
                    'cache_key'=>Env::get('redis.jwt').':admin:*'
                ],[
                    'cache'=>$cacheService,
                    'cache_key'=>'system:admin:roles:*'
                ],[
                    'cache'=>$cacheService,
                    'cache_key'=>'admin:login:*'
                ]];

                $cacheTitle = "后台登录缓存";
                break;
            case 'agent_login':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::JWT_REDIS_DRIVER);
                $cacheData = [[
                    'cache'=>$cacheService,
                    'cache_key'=>Env::get('redis.jwt').':agent:*'
                ]];
                $cacheTitle = "代理登录缓存";
                break;
            case 'product':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
                $cacheData = [[
                    'cache'=>$cacheService,
                    'cache_key'=>'product:*'
                ]];
                $cacheTitle = "产品数据缓存";
                break;
            case 'market':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
                $cacheData = [];
//                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
//                $cacheData = [[
//                    'cache'=>$cacheService,
//                    'cache_key'=>'product:*'
//                ],[
//                    'cache'=>$cacheService,
//                    'cache_key'=>'{queues:OrderPingCangLogJob}'
//                ],[
//                    'cache'=>$cacheService,
//                    'cache_key'=>'{queues:SetKDataJob}'
//                ]];
                $cacheTitle = "产品行情缓存";
                break;
            case 'system':
                $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_REDIS_DRIVER);
                $cacheData = [[
                    'cache'=>$cacheService,
                    'cache_key'=>'system_config'
                ]];
                $cacheTitle = "系统配置缓存";
                break;

        }
        foreach ($cacheData as $cacheItem){
            $it = null;
            $keysToDelete = [];
            $cacheClass = $cacheItem['cache'];
            while ($keys = $cacheClass->handler()->scan($it, $cacheItem['cache_key'])) {
                foreach ($keys as $key) {
                    $keysToDelete[] = $key;
                    // 可以选择在这里每找到一定数量的键就执行一次 del 操作，减少内存占用
                    if (count($keysToDelete) >= 100) { // 调整这个数值可以根据实际情况调整每次删除的键的数量
                        $cacheClass->handler()->del($keysToDelete);
                        $keysToDelete = [];
                    }
                }
            }
            // 确保剩余的键也被删除
            if (!empty($keysToDelete)) {
                $cacheClass->handler()->del($keysToDelete);
            }
        }
        $this->success($cacheTitle.'清理成功！');
    }
}