<?php
/**
 * @Date: 2025/6/25 22:59
 */
declare(strict_types=1);

namespace addon\smsbao\services;
use app\common\constants\CacheKeyConstant;
use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\CacheService;

/**
 * 支持
 * Class SmsService
 * @package addon\onesms\service
 */
class SmsService
{

    protected string $apiUrl;
    protected string $apiMethod;

    protected array $header = [];

    protected string $method = 'GET';

    public string $smsbaoUsername = "";

    protected string $smsbaoPassword = "";

    protected string $phone = '';

    protected mixed $config;

    protected string $tempCode;

    protected array $temps = [];

    protected array $temp = [];

    protected mixed $data;

    protected array $params = [];

    protected string $tempSign = '';
    public function __construct()
    {
        $this->config = json_decode(sysconf('addon_sms|raw'),true);
        $this->smsbaoUsername = $this->config['smsbao_username'];
        $this->smsbaoPassword = $this->config['smsbao_password'];
        $this->tempSign = $this->config['temp_sign'];
        if(!empty($this->config['temp_sign'])){
            $this->temps = $this->config['sms_temps'];
        }
    }

    public function send($params = [])
    {
        if (empty($params['phone'])) {
            throw new CommonException("手机号码不能为空！");
        }
        $this->phone = $params['phone'];
        $class = $params['temp_code'] ?? '';
        if (empty($class)) {
            $class = 'verifyCode';
        }
        $this->tempCode = $class;
        $this->getTemp();
        $this->$class();
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );

        $smsapi = "https://106.ihuyi.com";
        $content = $this->data;//要发送的短信内容
        $phone = $this->phone;//要发送短信的手机号码
        $this->params['account'] = 'C04952097';
        $this->params['password'] = "b883ea3c4be497b09f29342ab360f1af";
        $this->params['mobile'] = $phone;
        $this->params['content'] = $content;
        $this->params['time'] = time();
        $this->params['format'] = "json";
        $this->params['method'] = "Submit";
        $this->apiUrl = $smsapi;
        $this->apiMethod = 'webservice/sms.php';
        $this->method = "GET";
        $this->header[] = 'Content-Type: application/x-www-form-urlencoded';
        return $this->httpCurl();

    }


    public function httpCurl(){
        $url = $this->apiUrl .'/'.$this->apiMethod;
        if($this->method == 'GET'){
            $res = httpRequestGet($url.'?'.http_build_query($this->params));
        }else{
            $res = httpRequest($url,$this->params,$this->header);
        }
        $result = json_decode($res,true);
        if(isset($result['msg']) && $result['msg'] != "提交成功"){
            throw new CommonException($result['msg']);
        }
        return $result['smsid'] ?? $result;
    }

    /**
     *  验证码
     */
    public function verifyCode()
    {
        $code = StringHelper::randString(6);
        $cacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::MEMBER_REDIS_DRIVER);
        $cacheService->set('verify_code:phone:' . $this->phone, $code, 60 * 10);
//        $this->data = str_replace('{code}', $code, $this->temp['temp_content']);
        $param['code'] = $code;
        $this->data = "您的验证码是：".$code."。请不要把验证码泄露给其他人。";
//        $this->data = json_encode($param);
    }


    /**
     *  获取模板
     */
    public function getTemp()
    {
        foreach ($this->temps as $key => $item) {
            if ($item['temp_code'] == $this->tempCode) $this->temp = $item;
        }
    }
}