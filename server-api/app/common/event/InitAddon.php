<?php
/**
 * @Date: 2025/6/25 23:12
 */
declare(strict_types=1);
namespace app\common\event;

use think\facade\Event;

/**
 * 初始化插件
 */
class InitAddon
{

    ## 行为扩展的执行入口必须是run
    public function handle()
    {
        $this->initEvent();
    }
    /**
     * 初始化事件
     */
    private function InitEvent()
    {
        $cacheKey = "addon_event";
        if(!defined("initroute_tag")) exit();
        $addon  = ['smsbao'];
        $listen_array = [];
        foreach ($addon as $v) {
            if (file_exists(root_path().'addon/' . $v . '/config/event.php') ) {
                $addon_event = require_once root_path().'addon/' . $v . '/config/event.php';
                $listen = $addon_event['listen'] ?? [];
                if (!empty($listen)) {
                    $listen_array[] = $listen;
                }
            }

        }
        if (!empty($listen_array)) {
            foreach ($listen_array as $k => $listen) {
                if (!empty($listen)) {
                    Event::listenEvents($listen);
                }
            }
        }
    }
}