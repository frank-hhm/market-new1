<?php
/**
 * @Date: 2025/1/14 17:52
 */
declare(strict_types=1);

namespace app\api\controller\common;

use app\api\services\ProductService;
use app\common\enum\EnumFactory;
use app\common\exception\CommonException;
use app\common\helper\MailerHelper;
use app\common\helper\StringHelper;
use app\common\library\party\email\MailerService;
use app\common\library\publish\VersionService;
use app\common\services\ArticleService;
use app\common\services\BannerService;
use app\common\services\common\CacheService;
use app\common\services\common\CaptchaService;
use app\common\services\NoticeService;
use app\common\services\PaymentService;
use app\common\services\ProductCacheService;
use app\common\services\ProductSectorService;
use think\facade\Env;
use think\facade\Filesystem;
use think\facade\Log;
use think\facade\Request;


/**
 * Class Publics
 */
class Publics extends \app\api\controller\Base
{

    /**
     * 获取首页数据
     * @force(false)
     * @method(GET)
     */
    public function getHome()
    {
        $data = [];
        $data['notice'] = app(NoticeService::class)->getSelect();
        $data['banner'] = app(BannerService::class)->getSelect();
        $data['article'] = app(ArticleService::class)->getHomeSelect();
        $data['bind_status'] = empty($this->member['bind_status']['value']) ? 0 : 1;
        $productService = app(ProductService::class);
        $pros = $productService->getKLineHomeData(5,10);
        $data['activitys'] = sysconf('activitys|json');
        $this->success('获取成功',array_merge($data,$pros));
    }
    /**
     * 获取首页数据
     * @force(false)
     * @method(GET)
     */
    public function getProHome()
    {
        $data = [];
        $productService = app(ProductService::class);
        $pros = $productService->getKLineHomeData(5,10);

        $this->success('获取成功',$pros);
    }

    /**
     * 获取配置
     * @force(false)
     * @method(GET)
     */
    public function configData()
    {
        $data = [];
        $data['sms_test'] = 1;
        $data['about_content'] = sysconf('about_content');
        $data['about_content_know'] = sysconf('about_content_know');
        $data['about_role'] = sysconf('about_role');
        $data['kefu_qq'] = sysconf('kefu_qq');
        $data['kefu_wechat'] = sysconf('kefu_wechat');
        $data['system_icon'] = sysconf('system_icon');
        $data['system_logo'] = sysconf('system_logo');
        $data['system_name'] = sysconf('system_name');
        $data['about_bg'] = sysconf('about_bg');

        $data['about_product'] = sysconf('about_product');

        $data['member_withdrawal_rate'] = sysconf('member_withdrawal_rate');
        $data['member_usdt_withdrawal_rate'] = sysconf('member_usdt_withdrawal_rate');
        $data['member_alipay_withdrawal_status'] = sysconf('member_alipay_withdrawal_status');
        $data['login_banner'] = sysconf('login_banner');
        $data['payment_alipays'] = [
            'payment_alipays_status'=>sysconf('payment_alipays_status'),
        ];
        $data['kefu_other'] = getKefuOther();
        $data['usdt_rate'] = sysconf('usdt_rate');
        $data['usdt_out_rate'] = sysconf('usdt_out_rate');

        $data["member_default_product"] = sysconf('member_default_product|json');

        $data['about_content_follow'] = sysconf('about_content_follow');

        $this->success('获取成功',$data);
    }


    /**
     * 获取协议
     * @force(false)
     * @method(GET)
     */
    public function agreement()
    {
        $type = $this->request->param('type');
        $data = sysconf($type);
        $this->success('获取成功',[
            'content'=>$data
        ]);
    }

    /**
     * 生成验证码
     * @force(false)
     * @method(GET)
     */
    public function captcha()
    {
        $captcha = CaptchaService::instance()->initialize()->getAttrs();
        $this->success('生成验证码成功', $captcha);
    }
    /**
     * 获取系统信息
     * @force(false)
     * @method(GET)
     */
    public function systemInfo()
    {
        $data['system_name'] = sysconf('system_name');
        $data['system_version'] = sysconf('system_version');
        $data['system_logo'] = sysconf('system_logo');
        $data['system_icon'] = sysconf('system_icon');
        $data['copyright'] = sysconf('copyright');
        $this->success($data);
    }

    /**
     * 获取系统权举数据
     * @force(false)
     * @method(GET)
     */
    public function enum()
    {
        $this->success(EnumFactory::getAll());
    }

    /**
     * 获取邮箱验证码
     * @force(false)
     * @method(POST)
     */
    public function getEmailCode(): void
    {
        $data = $this->request->postMore([
            ['email', ''],
            ['type', ''],
            ['captcha_code', ''],
            ['captcha_uniqid', '']
        ]);

        if(!CaptchaService::instance()->check($data['captcha_code'], $data['captcha_uniqid'])) {
            throw new CommonException('验证码错误，请重新输入',702);
        }

        $mailService = app(MailerService::class);
        $mailService->addAddress($data['email'], $data['email']);
        $tempRes = MailerHelper::getTemplate($data['type'],$data['email']);
        !empty($tempRes['html']) && $mailService->setHtml(true);
        // 发送邮件
        if ($mailService->send($tempRes['title'], $tempRes['content'])) {
            $this->success('发送成功');
        }
        $this->error($mailService->getError() ?: '发送失败');
    }

    /**
     * 发送短信
     * @force(false)
     * @method(POST)
     */
    public function sendSms()
    {
        event("SendSms",$this->request->param());
        $this->success('发送成功');
    }
    /**
     * 发送验证码
     * @force(false)
     * @method(POST)
     */
    public function sendCode()
    {

        $params = $this->request->getMore([
            ['type', ''],
            ['number',''],
            ['temp',''],
        ]);
        if ($params["type"] === "phone"){
            event("SendSms",$this->request->param());
            $this->success('发送成功');
        }elseif ($params["type"] === "email"){
            $mailService = app(MailerService::class);
            $mailService->addAddress($params['number'], $params['number']);
            $tempRes = MailerHelper::getTemplate($params['temp'],$params['number']);
            !empty($tempRes['html']) && $mailService->setHtml(true);
            // 发送邮件
            if ($mailService->send($tempRes['title'], $tempRes['content'])) {
                $this->success('发送成功');
            }

            $this->error($mailService->getError() ?: '发送失败');
        }
    }
    /**
     * 发送短信
     * @force(false)
     * @method(POST)
     */
    public function sendSmsByCaptcha()
    {
        $data = $this->request->postMore([
            ['captcha_code', ''],
            ['captcha_uniqid', '']
        ]);
        if(!CaptchaService::instance()->check($data['captcha_code'], $data['captcha_uniqid'])) {
            throw new CommonException('图形验证码错误，请重新输入',702);
        }

        event("SendSms",$this->request->param());
        $this->success('发送成功');
    }

    /**
     * 获取支付渠道
     * @force(false)
     * @method(GET)
     */
    public function getPaymentSelect(){
        $agentId = 0;
        if(!empty($this->member)){
            $agentId = $this->member['agent_id'];
        }
        $paymentService = app(PaymentService::class);
        $lists = $paymentService->getSelect($agentId);
        $this->success('获取成功',$lists);
    }

    /**
     * 获取支付渠道
     * @force(false)
     * @method(GET)
     */
    public function getPaymentConfig(){
        $data = $this->request->getMore([
            ['id', 1],
        ]);
        $paymentService = app(PaymentService::class);
        $detail = $paymentService->getDetail($data['id']);
        $this->success('获取成功',$detail);
    }

    /**
     * app下载数据
     * @force(false)
     * @method(GET)
     */
    public function getDownloadApp(){
        if(Env::get('env.env_name') === "uat"){
            $downloadData = getUatDownloadData();
        }else{
            $downloadData = getDownloadData();
        }
        $ios = $downloadData['ios'] ?? [];
        $this->success('获取成功',[
            'android'=>$downloadData['android'],
            'ios' => $ios
        ]);
    }
    /**
     * 获取app更新
     * @force(false)
     * @method(POST)
     */
    public function appUpgrade(){
        $data = $this->request->getMore([
            ['version',''],
        ]);
//        Log::write(json_encode($this->request->param(),JSON_UNESCAPED_UNICODE),'error');
        $request = Request::instance();
        $defaultVersion = sysconf("default_version|json");
        $h5 = "h5";
        if(Env::get('env.env_name') === "uat"){
            $h5 = "h5uat";
        }
        $downloadUrl =  $request->domain() .'/'.$h5.'/#/pages/other/download';
        $status = 0;
        $wgtUrl = "";
        if(app(VersionService::class)->checkVersion($defaultVersion['version'],$data['version']) ){
            if($defaultVersion["is_app"] == 1){
                $status = 1;
            }
            if ($defaultVersion["is_wgt"] == 1){
                $status = 2;
                $wgtName = $defaultVersion['wgt_name'];
                if(Env::get('env.env_name') === "uat"){
                    $wgtName = $defaultVersion['wgt_uat_name'];
                }
                $wgtUrl = $request->domain() . '/uploads/storage/app/'.$wgtName;
            }
        }

        $note = $defaultVersion["desc"];
        empty($note) && $note = "更新已知内容";
        //status 0不需要，1需要
        $this->success('获取成功',[
            'rsp' => [
                'note'=>$note,
                'url'=>$downloadUrl,
                'wgturl'=>$wgtUrl,
                "status"=>$status,
                'to_version'=>$defaultVersion["version"]
            ],
        ]);
    }

    /**
     * 发布新版本
     * @force(false)
     * @method(POST)
     */
    public function publishVersionDefault(){
        $data = $this->request->param();
        // 发布版本
        $versionService = app(VersionService::class);
        if($versionService->check($data) && $res = $versionService->install()){
            $this->success('提交发布事务成功',[
                "publish_id"=>$res
            ]);
        }
        $this->error($versionService->getError()?:'提交发布事务失败');
    }
    /**
     * 采集
     * @force(false)
     * @method(GET)
     */
    public function gather(){
        if($res = app(ArticleService::class)->gather()){
            $this->success('执行成功');
        }
        $this->error('执行失败');
    }


    /**
     * 上传图片
     * @force(false)
     * @method(POST)
     */
    public function uploadImage()
    {
        $file = request()->file('image');
        if (empty($file)){
            $this->error('上传失败：文件为空!');
        }
        $publicDisk = Filesystem::getDiskConfig('public');
        $saveFileName  = '/'.Filesystem::disk('public')->putFile('voucher', $file,'uniqid');
        $filePath = $publicDisk['url'] . $saveFileName;
        $fileUrl = sysconf("web_domain"). $filePath;
        if ($saveFileName) {
            $this->success('上传成功',['voucher_url' => $fileUrl]);
        }
        $this->error('上传失败');
    }
}