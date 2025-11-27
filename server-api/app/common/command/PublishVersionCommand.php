<?php
/**
 * @Date: 2025/7/7 19:37
 */
declare(strict_types=1);

namespace app\common\command;

use app\common\jobs\TestJob;
use app\common\library\api\PublishVersionService;
use app\common\library\publish\VersionService;
use app\common\services\VersionDataService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Env;
use think\facade\Log;

class PublishVersionCommand extends Command
{
    protected function configure()
    {
        $this->setName('TestCommand');
    }

    protected function execute(Input $input, Output $output)
    {

        if(Env::get('env.env_name') !== "dev"){
            echo "请使用dev环境\n";exit;
        }
        $data = [
            'version' => '1.0.43',
            'desc' => '新版上线（43）',
            //是否有APP更新
            "is_app"=>0,
            //app报名
            "app_name"=>"PRO__9B5AAD3__20250820221617.apk",
            "app_uat_name"=>"",
            //是否为wgt包
            "is_wgt"=>1,
            "wgt_uat_name"=>"_UAT_1036__9B5AAD3.wgt",
            //wgt包名称
            "wgt_name"=>"_PRO_1043__9B5AAD3.wgt",
        ];
        $versionService = app(VersionService::class);
//        if(!$versionService->check($data)){
//            echo $versionService->getError()?:"提交出错！";
//            echo "\n";
//            exit;
//        }

        Db::startTrans();
        $versionId = app(VersionDataService::class)->dao->model->insertGetId(array_merge($data, [
            'status' => 1,
            'create_time' => time(),
        ]));

        if($versionId){
            $res = app(PublishVersionService::class)->send($data);
            if(isset($res['code']) && $res['code'] !== 1){
                Db::rollback();
                echo "发布失败【".$res["message"]."】\n";exit;
            }else{
                sysconf('default_version',json_encode($data,JSON_UNESCAPED_UNICODE));
                Db::commit();
                echo "发布成功【".json_encode($res,JSON_UNESCAPED_UNICODE)."】\n";exit;
            }
        }else{
            Db::rollback();
            echo "发布失败\n";exit;
        }
        echo "test";exit;
    }
}