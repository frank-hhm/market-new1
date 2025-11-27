<?php
/**
 * @Date: 2025/7/7 17:13
 */
declare(strict_types=1);
namespace app\common\jobs;

use app\common\helper\ArrayHelper;
use app\common\services\VersionDataService;
use think\facade\Db;
use think\facade\Env;
use think\facade\Log;
use think\queue\Job;
use think\Exception;

class VersionPublishJob
{

    public string $message = "";
    public function fire(Job $job, $data)
    {
        try {
            if ($this->publishVersion($data)) {
                echo "版本更新 成功\n";
                $job->delete();
            } else {
                echo "版本更新 失败\n";
                if ($job->attempts() > 3) { // 如果尝试超过3次，则将任务记录为失败
                    $job->failed();
                } else {
                    $job->release(10); // 否则延迟10秒后重新尝试
                }
            }
        } catch (\Exception $e) {
            $job->failed($e);
        }
    }

    public function publishVersion($data)
    {
        if(empty($data['id']) || empty($data['version'])){
            return false;
        }
        if(!$this->runGit()){
            return false;
        }

        if(!$this->runDatabase()){
            return false;
        }

        //执行版本更新
        Db::startTrans();
        $update['publish_time'] = time();
        try {
            $update['status'] = 1;
            //如果是生产环境
            if(Env::get('env.env_name') == "pro"){
                $update['status'] = 22;
            }
            $message = "执行版本更新成功：【".$data["version"]."】";
            $update['publish_log'] = $this->message . "\n";
            $update['publish_log'] .= $message;
            $res[] = app(VersionDataService::class)->dao->model->where('id',$data['id'])->update($update);
            //设置默认版本
            sysconf("default_version", json_encode($data,JSON_UNESCAPED_UNICODE));
            if(ArrayHelper::checkArr($res)){
                Db::commit();
                Log::write($message,'publish');
                return $res;
            }
            return false;

        }catch (\Exception $e){
            Db::rollback();
            $message = "执行版本更新失败：【".$e->getMessage()."】";
            $update['publish_log'] = $this->message . "\n";
            $update['publish_log'] .= $message;
            $update['status'] = 11;
            Log::write($message,'publish');
            app(VersionDataService::class)->dao->model->where('id',$data['id'])->update($update);
            return false;
        }
        // ...任务失败后的逻辑...
    }

    /**
     * 执行更新git
     */
    public function runGit(): bool
    {

        $command = 'git pull origin master';
        $output = [];
        $returnStatus = 0;
        // 执行命令，并获取输出
        exec($command, $output, $returnStatus);
        if ($returnStatus !== 0) {
            $message = "命令git pull执行失败：【".print_r($output, true)."】";
            $this->message .= $message."\n";
            Log::write($message,'publish');
            return false;
        } else {
//            echo "命令执行成功。\n";
            $message = "命令git pull执行成功：【".print_r($output, true)."】";
            $this->message .= $message."\n";
            Log::write($message,'publish');
            return true;
        }
    }

    /**
     * 执行更新数据库操作
     */
    public function runDatabase(): bool
    {

        $command = 'php think migrate:run';
        $output = [];
        $returnStatus = 0;

        // 执行命令，并获取输出
        exec($command, $output, $returnStatus);
        if ($returnStatus !== 0) {
            $message = "命令php think migrate:run执行失败：【".print_r($output, true)."】";
            $this->message .= $message."\n";
            Log::write($message,'publish');
            return false;
        } else {
            $message = "命令php think migrate:run执行成功：【".print_r($output, true)."】";
            $this->message .= $message."\n";
            Log::write($message,'publish');
            return true;
        }
    }
}