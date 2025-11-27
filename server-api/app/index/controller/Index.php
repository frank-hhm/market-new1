<?php
/**
 * @Date: 2023/12/16 13:32
 */
declare(strict_types=1);
namespace app\index\controller;

use app\BaseController;
use app\common\enum\EnumFactory;
use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\helper\QrcodeHelper;
use app\common\helper\StringHelper;
use app\common\services\ArticleService;
use app\common\services\common\CaptchaService;
use app\common\services\ProductService;
use think\facade\Env;
use think\facade\Request;

class Index extends BaseController
{

    /**
     * @method(GET)
     */
    public function index()
    {
        $request = Request::instance();
        header('Location:' . $request->domain() . '/h5');
        exit();
    }


    /**
     * @method(GET)
     */
    public function download(){
        if(Env::get('env.env_name') === "uat"){
            $downloadData = getUatDownloadData();
        }else{
            $downloadData = getDownloadData();
        }
        header('Location:' . $downloadData['android']['download_url']);
    }

    /**
     * @method(GET)
     */
    public function downloadApk(){
        $request = Request::instance();

        if(Env::get('env.env_name') === "uat"){
            $downloadData = getUatDownloadData();
        }else{
            $downloadData = getDownloadData();
        }
        $androidName = $downloadData['android']['apk_name'] ?? '';
        header('Location:' . $request->domain() . '/uploads/storage/app/'.$androidName.'.apk');
    }

    /**
     * 生成验证码
     * @method(GET)
     */
    public function captcha()
    {
        $captcha = CaptchaService::instance()->initialize()->getAttrs();
        $this->success('生成验证码成功', $captcha);
    }
    /**
     * 获取系统信息
     * @method(GET)
     */
    public function systemInfo()
    {
        $data['system_name'] = sysconf('system_name');
        $data['system_version'] = sysconf('system_version');
        $data['system_logo'] = sysconf('system_logo');
        $data['system_icon'] = sysconf('system_icon');
        $data['copyright'] = sysconf('copyright');
        $data['usdt_rate'] = sysconf('usdt_rate');
        $this->success($data);
    }

    /**
     * 获取公共信息
     * @method(GET)
     */
    public function getCommonInfo()
    {
        $data['system_name'] = sysconf('system_name');
        $data['system_version'] = sysconf('system_version');
        $data['system_logo'] = sysconf('system_logo');
        $data['system_icon'] = sysconf('system_icon');
        $data['copyright'] = sysconf('copyright');
        $data['kefu_other'] = getKefuOther();

        $h5 = "h5";

        if(Env::get('env.env_name') === "uat"){
            $downloadData = getUatDownloadData();
            $h5 = "h5uat";
        }else{
            $downloadData = getDownloadData();
        }

        $data['download_android'] = $downloadData['android']['download_url'] ?? '';
        $data['download_ios'] = $downloadData['ios']['download_url'] ?? '';

        $qrcode = app(QrcodeHelper::class);
        $downloadH5 = baseUrl().'/'.$h5.'/#/pages/other/download';
        $data['qrcode_h5'] = baseUrl().$qrcode->getImage($downloadH5,[]);
        $this->success($data);
    }

    /**
     * 获取系统权举数据
     * @method(GET)
     */
    public function enum()
    {
        $this->success(EnumFactory::getAll());
    }


    /**
     *
     * @force(false)
     * @method(GET)
     */
    public function getArticleList()
    {
        $data = app(ArticleService::class)->getList($this->request->param());
        $this->success('获取成功',$data);
    }

    /**
     * 文章详细
     * @force(false)
     * @method(GET)
     */
    public function getArticleDetail(){
        $this->success('获取成功',app(ArticleService::class)->getDetail($this->request->get('id')));
    }

    public function getProductMarket(){

        $productService = app(ProductService::class);

        $select = $productService->dao->model->with("product_data")->where("is_hot",1)->where("status","1")->order(["sort"=>"DESC"])->select()->toArray();
        if(count($select) < 4){
            $ids = array_column($select, 'id');
            $select2 = $productService->dao->model->with("product_data")->where("is_hot",0)->where("status","1")->where("id","NOTIN",$ids)->limit(4 - count($select))->order(["sort"=>"DESC"])->select()->toArray();
            $select = array_merge($select,$select2);
        }
        $this->success("success",$select);
    }
}