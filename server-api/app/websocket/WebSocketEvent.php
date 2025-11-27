<?php
/**
 * @Date: 2025/6/23 16:43
 */
declare(strict_types=1);
namespace app\websocket;

use app\common\constants\StringConstant;
use app\common\services\member\MemberAuthService;
use app\websocket\services\PushMessageService;
use EasySwoole\Component\TableManager;

class WebSocketEvent
{
    public static  function onOpen($server, $request): void
    {
//      RedisCacheService::instance()->set(self::$cacheKey,$request->fd);
//      echo $request->fd . '链接成功' . PHP_EOL;
        $fd =  (string)$request->fd;
        $fbId = $request->get['fb_id'] ?? 'anonymous'; // 或者从其他地方获取
        // 获取 table 实例
        $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);
//      var_dump($table);
        if(!empty($table)){
            $table->set($fd, [
                'fb_id' =>$fd,
                'last_time' => microtime(true) // 记录连接建立的时间
            ]);
        }
    }
    public static  function onMessage($server, $frame): void
    {
        try {
            // 获取表
            $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);
            $server = \EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer();
            $fdKey = (string)$frame->fd;
            if (!empty($table) && $table->exist($fdKey)) {
                // 获取整行数据
                $row = $table->get($fdKey);
                // 修改某个字段
                $row['last_time'] = microtime(true);
                $data = json_decode($frame->data, true);
                if (!empty($data['type']) && $data['type'] == 'ping') {
                    $row['market_subscribe'] = "all";
                    if ($server->exist((int)$fdKey)) {
                        $server->push((int)$fdKey, json_encode(['type' =>"productCache", 'data' => []],JSON_UNESCAPED_UNICODE));
                    }
//                    if(rand(1, 6) > 3){
//                        app(PushMessageService::class)->push('productCache',$data);
//                    }
                    if(!empty($data['market_subscribe'])){
//                        $marketSubscribe = (string)$data['market_subscribe'] ?? 'all';
//                        $row['market_subscribe'] = $marketSubscribe;
                    }
                    if(!empty($data['token']) && !empty($member = app(MemberAuthService::class)->isLogin($data['token']))){
                        $row['uid'] = $member['id'];
                    }
                }
                // 设置回去（覆盖整行）
                $table->set($fdKey, $row);
            }
//                $dispatch->dispatch($server, $frame->data, $frame);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
    /**
     * 握手事件
     */
    public  function onHandShake( $request, $response): bool
    {
        // 通过自定义握手 和 RFC ws 握手验证
        if ($this->customHandShake($request, $response) && $this->secWebsocketAccept($request, $response)) {
            // 接受握手 还需要101状态码以切换状态
            $response->status(101);
            $response->end();
            return true;
        }
        $response->end();
        return false;
    }
    /**
     * 关闭事件
     */
    public static  function onClose( $server, int $fd, int $reactorId): void
    {
        $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);
        if (!empty($table) && $table->exist((string)$fd)) {
//            $fbId = $table[$fd]['fb_id'];
            $table->del( (string)$fd);
        }
    }
    /**
     * 自定义握手事件
     * 在这里自定义验证规则
     */
    protected function customHandShake($request,$response): bool
    {
        $headers = $request->header;
        $cookie = $request->cookie;

        // if (如果不满足我某些自定义的需求条件，返回false，握手失败) {
        //    return false;
        // }
        return true;
    }
    /**
     * RFC规范中的WebSocket握手验证过程                    是否通过验证
     */
    protected function secWebsocketAccept( $request, $response): bool
    {
        // ws rfc 规范中约定的验证过程
        if (!isset($request->header['sec-websocket-key'])) {
            // 需要 Sec-WebSocket-Key 如果没有拒绝握手
            return false;
        }
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
            || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
        ) {
            //不接受握手
            return false;
        }
        $key = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $headers = array(
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
            'KeepAlive'             => 'off',
        );
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }
        // 发送验证后的header
        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }
        return true;
    }
}