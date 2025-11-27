<?php
/**
 * @Date: 2025/7/7 14:44
 */
declare(strict_types=1);

namespace app\sys\controller;
use app\common\library\api\PublishVersionService;
use think\facade\App;
use app\common\services\VersionDataService;
use think\facade\Db;
use think\facade\Env;

/**
 * 版本
 * Class VersionData
 * @package app\sys\controller
 */
class VersionData extends Base
{
    /**
     * VersionData constructor.
     * @param App $app
     * @param VersionDataService $service
     */
    public function __construct(App $app, VersionDataService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }



    /**
     * 版本列表
     * @noAuth(true)
     * @method(GET)
     */
    public function getList()
    {
        $data = $this->request->postMore([
        ]);
        $this->success($this->service->getList($data));
    }


    /**
     * 获取版本信息
     * @noAuth(true)
     * @method(GET)
     */
    public function versionDefault()
    {
        $data = sysconf('default_version|json');
        $data['env_name'] = Env::get('env.env_name');
        if(Env::get('env.env_name') !== "dev"){
            $versionData =  app(VersionDataService::class)->dao->model->where('status',0)->order(['create_time' => "desc"])->find();
            if(empty($versionData)){
                $data['new_version'] = $data["version"];
            }else{
                $data['new_version'] = $versionData["version"];
            }
        }else{
            $data['new_version'] = Env::get('env.env_new_version') ??  '';
        }
        $this->success('获取成功！',$data);
    }
    /**
     * 发布版本
     * @method(POST)
     */
    public function publish()
    {
        $data = $this->request->postMore([
            ['id',0],
        ]);

        //如果测试环境
        if(Env::get('env.env_name') !== "uat"){
            $this->error('非测试环境，请勿推送版本！');
        }

        $versionData =  app(VersionDataService::class)->dao->model->where('id',$data['id'])->order(['create_time' => "desc"])->find();
        if(empty($versionData)){
            $this->error('版本信息不存在！');
        }
        $versionData = $versionData->toArray();

        Db::startTrans();
        $versionId = app(VersionDataService::class)->dao->model->where([
            'id' => $data['id'],
        ])->update(array_merge($data, [
            'status' => 2,
            'publish_time' => time(),
        ]));
        if($versionId){
            $res = app(PublishVersionService::class)->send([
                'version' => $versionData['version'],
                "is_app"=>$versionData["is_app"],
                "is_wgt"=>$versionData["is_wgt"],
                "desc"=>$versionData["desc"],
                "app_name"=>$versionData["app_name"],
                "wgt_name"=>$versionData["wgt_name"],
                "app_uat_name"=>$versionData["app_uat_name"],
                "wgt_uat_name"=>$versionData["wgt_uat_name"]
            ]);
            if(isset($res['code']) && $res['code'] !== 1){
                Db::rollback();
                app(VersionDataService::class)->dao->model->where('id',$data['id'])->update([
                    'status' => 3,
                    'publish_time' => time(),
                    'publish_log' => '发布失败【'.$res["message"].'】',
                ]);
                $this->error('发布失败【'.$res["message"].'】');
            }
            sysconf('default_version',json_encode($versionData,JSON_UNESCAPED_UNICODE));
            Db::commit();
            $this->success('发布成功');
        }else{
            Db::rollback();
            $this->error('发布失败');
        }
    }

}