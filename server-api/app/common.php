<?php
// 应用公共文件
use app\common\helper\ArrayHelper;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Env;
use think\facade\Event;
use think\facade\Request;
use think\facade\Log;

if (!function_exists('sysconf')) {
    /**
     * 配置系统参数
     */
    function sysconf($name, $value = null)
    {
        static $data = [];
        list($field, $raw) = explode('|', "{$name}|");
        if ($value !== null) {
            Cache::store("redis")->delete("system_config", $data);
            list($row, $data) = [['sys_name' => $field, 'sys_value' => $value], []];
            $wh = Db::name('system_config')->where('sys_name','=',$field)->find();
            return Db::name('system_config')->where($wh)->save($row);
        }
        if (empty($data)) {
            $data = Cache::store("redis")->get('system_config');
            if (empty($data)) {
                $data = Db::name('system_config')->column('sys_value','sys_name');
                Cache::store("redis")->set("system_config", $data);
            }
        }
        if (isset($data[$field])) {
            if (empty($raw) || strtolower($raw) === 'raw') {
                return $data[$field];
            } elseif (strtolower($raw) === 'json') {
                return ArrayHelper::objectToArray(json_decode($data[$field]));
            }
        } else {
            return '';
        }
    }
}
/**
 * 事件
 */
if (!function_exists('event')) {
    function event($event, $args = [], $once = true)
    {
        $res = Event::trigger($event, $args);
        if (is_array($res)) {
            $res = array_filter($res);
            sort($res);
        }
        //只返回一个结果集
        if ($once) {
            return $res[0] ?? [];
        }
        return $res;
    }
}


if (!function_exists('_log')) {
    function _log(string $title = '',string | array $data = [], string $level = 'info',$isParam = false): void
    {
        $time = date('Y-m-d H:i:s',time());
        !empty($title) && Log::write($time."【 ".$title." 】",$level);
        if(is_array($data) ){
            foreach ($data as $item){
                is_array($item) && $item = json_encode($item,JSON_UNESCAPED_UNICODE);
                Log::write($item,$level);
            }
        }else{
            Log::write($data,$level);
        }
        if($isParam) Log::write(json_encode(request()->param(),JSON_UNESCAPED_UNICODE));
        Log::write("=====================================================End",$level);
    }
}


//判断文件夹是否存在，没有则新建。
if (!function_exists('mkdirs')) {
    function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!mkdirs(dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }
}

/**
 * curl请求指定url(POST请求)
 * $url请求的URL
 * $data 请求传递的数据
 */
function httpRequest($url,$data = null,$headers = [ 'Content-Type: application/json' ],$motach = "POST",$isFile = false): bool|string
{

    $curl = curl_init();
    if( count($headers) >= 1 ){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        if(is_array($data)) {
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $motach);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
/**
 * 模拟GET请求 HTTPS的页面
 * @param string $url 请求地址
 * @return string $result
 */
function httpRequestGet(string $url): string
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}


if (!function_exists('go')) {
    function go(callable $callback, ...$params)
    {
        return Swoole\Coroutine::create(function () use ($callback, $params) {
            echo "swoole go\n";
            return call_user_func_array($callback, $params);
        });
    }
}

if (!function_exists('baseUrl')) {
    function baseUrl()
    {
        return "https:".sysconf("web_domain");
        if(Env::get('env.env_name') === "uat"){
            return 'http://nuat1.clwft.cn/';
        }else{
            return  'http://www.clwft.cn/';
        }
    }
}

if (!function_exists('getUatDownloadData')) {
    function getUatDownloadData()
    {
        return [
            'android' => [
                'apk_name'=>'UAT__9B5AAD3__20250711044621',
                'download_url'=>sysconf('android_download_url') ?? ''
            ],
            'ios' => [
                'download_url'=>sysconf('ios_download_url') ?? 'https://gjdzo.voofwrlr.com/api/c/3dlkiuye'
            ]
        ];
    }
}

if (!function_exists('getDownloadData')) {
    function getDownloadData()
    {
        //uat __UNI__9B5AAD3__20250708225025
        //pro PRO__9B5AAD3__20250708231206
        return [
            'android' => [
                'apk_name'=>'PRO__9B5AAD3__20250820221617',
                'download_url'=>sysconf('android_download_url') ?? ''
            ],
            'ios' => [
                'download_url'=>sysconf('ios_download_url') ?? 'https://gjdzo.voofwrlr.com/api/c/3dlkiuye'
            ]
        ];
    }
}

if (!function_exists('getKefuOther')) {
    function getKefuOther()
    {
        if (empty(sysconf("kefu_other"))){
            return "https://web.jiaxincloud.com/onekey.html?id=n3vrnm5xzxnyba&appName=ssj735&appChannel=20002";
        }else{
            return sysconf("kefu_other");
        }
    }
}

//uat 测试环境地址
if (!function_exists('baseEnvUrl')) {
    function baseEnvUrl()
    {
        if(empty(Env::get('env.env_version_url'))){
            throw new \app\common\exception\CommonException("请配置环境地址");
        }
        return Env::get('env.env_version_url');
    }
}
