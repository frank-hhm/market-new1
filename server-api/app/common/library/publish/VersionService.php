<?php
/**
 * @Date: 2025/7/7 16:19
 */
declare(strict_types=1);


namespace app\common\library\publish;


use app\common\services\VersionDataService;
use think\facade\Queue;

class VersionService
{
    // 错误信息
    protected mixed $error;

    protected mixed $defaultVersion;

    protected mixed $installData = [];

    public function __construct()
    {
        $this->defaultVersion = sysconf("default_version|json");

    }

    public function isValidVersion($version)
    {
        // 正则匹配常见版本号格式，支持 pre-release 和 build metadata
        $pattern = '/^
        (?:\d+\.){2}\d+            # 主版本号.次版本号.修订号，如 1.0.0
        (?:-[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*)?  # 可选的预发布版本 -alpha, -beta.2
        (?:\+[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*)?  # 可选的构建元数据 +build.123
    $/x';

        return preg_match($pattern, $version) === 1;
    }
    public function check($data){
        $this->installData = $data;
        if (empty($this->installData["version"])) {
            $this->error = '要更新的版本不能为空';
            return false;
        }
        if (!$this->isValidVersion($this->installData["version"])) {
            $this->error = '要更新的版本格式不正确';
            return false;
        }
        if(!$this->checkPhpVersion()){
            return false;
        }

        $currentVersion = $this->defaultVersion["version"] ?? '1.0.0';
        $targetVersion = $this->installData["version"];
//        dump($currentVersion);
//        dump($targetVersion);die;
        // 使用 version_compare 比较两个版本号
        if (version_compare($targetVersion, $currentVersion) > 0) {
            return true;
        } elseif (version_compare($targetVersion, $currentVersion) == 0) {
            $this->error = "需更新版本号与当前版本号相同";
            return false;
        }else{
            $this->error = "需更新版本号（{$targetVersion}）低于当前版本号（{$currentVersion}）。";
            return false;
        }
    }

    public function checkVersion($targetVersion,$currentVersion)
    {
        // 使用 version_compare 比较两个版本号
        if (version_compare($targetVersion, $currentVersion) > 0) {
            return true;
        } elseif (version_compare($targetVersion, $currentVersion) == 0) {
            $this->error = "需更新版本号与当前版本号相同";
            return false;
        }else{
            $this->error = "需更新版本号（{$targetVersion}）低于当前版本号（{$currentVersion}）。";
            return false;
        }
    }

    public function install()
    {
        $queueData = $this->installData;
        $this->installData["create_time"] = time();
        $this->installData["status"] = 0;
        $queueData["id"] = app(VersionDataService::class)->dao->model->insertGetId($this->installData);
        Queue::push("app\common\jobs\VersionPublishJob", $queueData, 'VersionPublishJob');
//        if(!$this->runGit()){
//            return false;
        return $queueData["id"];
    }


    /**
     * 检测php版本
     */
    public function checkPhpVersion(){
        // 执行 php -v 命令获取输出
        $output = [];
        $return_var = 0;

        exec('php -v', $output, $return_var);

        if ($return_var !== 0) {
            $this->error = "请正确安装PHP环境。";
            return false;
        }

        // 解析输出的第一行（包含版本号）
        $version_line = $output[0]; // 通常第一行是类似 "PHP 8.3.1 (cli)"
        preg_match('/PHP (\d+\.\d+\.\d+)/', $version_line, $matches);

        if (!isset($matches[1])) {
            $this->error = "请正确安装PHP环境。";
            return false;
        }

        $current_version = $matches[1];

        $yesVersion = '8.3.0';

        // 判断版本是否 >= 8.3
        if (version_compare($current_version, $yesVersion, '>=')) {
            return true;
        } else {
            $this->error = "当前 PHP 版本过低。请安装 PHP ".$yesVersion." 或更高版本。";
            return false;
        }
    }
    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError(): mixed
    {
        return $this->error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }

    /**
     * 设置错误信息
     * @param $error
     */
    public function setError($error): void
    {
        empty($this->error) && $this->error = $error;
    }
}