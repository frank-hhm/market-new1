<?php
/**
 * @Date: 2024/3/20 16:17
 */
declare(strict_types=1);

namespace app\common\command;

use app\common\constants\CacheKeyConstant;
use app\common\constants\StringConstant;
use app\common\helper\StringHelper;
use app\common\jobs\TestJob;
use app\common\services\common\CacheService;
use EasySwoole\Component\TableManager;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('TestCommand');
    }

    protected function execute(Input $input, Output $output)
    {

        echo "开始处理\n";
        $walletSelect = Db::name("finance_water")->where("source","in",[
            "follow_revenue_commission","follow_revenue_commission2"
        ])->select()->toArray();
        foreach ($walletSelect as $item){
            $followOrderMember = Db::name("follow_order")->where("id",$item['source_id'])->find();
            if(!empty($followOrderMember)){
                Db::name("finance_water")->where("id",$item['id'])->update([
                    "other_id"=>$followOrderMember['member_id']
                ]);
            }
            echo "处理数据完成【".$item['id']."】\n";
        }

        die;

        $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);
        if ($table) {
            echo "当前在线人数：" . $table->count() . "\n";
        }else {
            echo "表未找到或尚未初始化。\n";
        }
        die;
        $orderNoData = ["2025082850102519","2025082950975057","2025082910151565","2025082955984956","2025082951489799","2025082997101515","2025082949575153"
            ,"2025082955101555","2025082910152525","2025082956975697","2025082910156485","2025082953521009","2025082999575756","2025082956535448","2025082910097515"];
        $select = Db::name("order")->where("orderno","in",$orderNoData)->select();
        Db::startTrans();

        foreach ($select as $item){
//            Db::
        }

        Db::commit();

        die;
// 获取表实例
        $table = TableManager::getInstance()->get(StringConstant::MARKET_WEBSOCKET_CLIENTS);

        if ($table) {
            // 清空表内的所有行数据
            $table->clear();
        } else {
            echo "表未找到或尚未初始化。\n";
        }
        die;
//        $this->handleMember();
//        $this->handleWallet();
//        $this->handleOrder();
//        $this->handleAgent();
//        $this->handleTotalYingkui();
//        $this->handleAgentLevel();
        app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->set(CacheKeyConstant::PRODUCT_PRICE.":49",3355.66,60);
        app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->set(CacheKeyConstant::PRODUCT_PRICE.":38",69.178,60);
        app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER)->set(CacheKeyConstant::PRODUCT_PRICE.":36",38.477,60);
        echo "test";exit;
    }
    public function handleTotalYingkui(){

        echo "开始处理钱包\n";
        $walletSelect = Db::name("member_coin")->select()->toArray();
        foreach ($walletSelect as $item){
            Db::name("member_coin")->where("id",$item['id'])->update([
                'yingkui_total'=> Db::name("order")->where([
                    'member_id'=>$item['member_id']
                ])->sum("yingkui")
            ]);
            echo "处理钱包数据完成【".$item['id']."】\n";
        }
    }

    //处理用户
    public function handleMember(){
        echo "开始处理用户\n";
        $memberSelect = Db::name("member_copy1")->select()->toArray();
        $memberData = [];
        $count = 0;
        foreach ($memberSelect as $item){
            $_item = $item;
            $_item['last_ip'] = "";
            $_item['last_time'] = 0;
            $_item['login_count'] = 0;
            $res = Db::name("member")->insert($_item);
//            $memberData[] = $_item;
            echo "处理钱包：【".$count."】:::".$res."\n";
        }
//        Db::name("member")->insertAll($memberData);
        echo "处理用户数据完成\n";
    }

    //处理用户钱包
    public function handleWallet(){
        echo "开始处理钱包\n";
        $walletSelect = Db::name("member_coin_copy1")->select()->toArray();
        $walletData = [];
        $count = 0;
        foreach ($walletSelect as $item){
            $_item = $item;
            $_item['yingkui_total'] = 0;
            $res = Db::name("member_coin")->insert($_item);
            echo "处理钱包：【".$count."】:::".$res."\n";
//            $walletData[] = $_item;
        }
//        Db::name("member_coin")->insertAll($walletData);
        echo "处理钱包数据完成\n";
    }


    //处理订单
    public function handleOrder(){
        echo "开始处理订单\n";
        $orderSelect = Db::name("order_copy1")->select()->toArray();
        $orderData = [];
        $count = 0;
        foreach ($orderSelect as $item){
            $_item = $item;
            $_item['ostyle'] = $item['ostyle']  == 0 ? 2 : 1;
            $count++;
            $res = Db::name("order")->insert($_item);
            echo "处理订单数据：【".$count."】:::".$res."\n";
        }
        echo "处理订单数据完成：\n";
    }

    //处理代理商
    public function handleAgent(){
        echo "开始处理代理商\n";
        $agentSelect = Db::name("system_admin_copy1")->where("level",'>',1)->where("delete_time",0)->select()->toArray();
        $agentData = [];
        foreach ($agentSelect as $item){
            $agentData[] = [
                'id' => $item['id'],
                'account' => $item['account'],
                'avatar' => $item['avatar'],
                'pwd' => $item['pwd'],
                'real_name' => $item['real_name'],
                'roles' => $item['roles'],
                'last_ip' => $item['last_ip'],
                'last_time' => $item['last_time'],
                'login_count' => $item['login_count'],
                'level' => $item['level'] - 2,
                'status' => $item['status'],
                'pid' => $item['pid'],
                'p_nickname' => $item['p_nickname'],
                'ratio' => $item['ratio'],
                'ratio_fee' => $item['ratio_fee'],
                'balance' => $item['balance'],
                'invite_code' => $item['invite_code'],
                'ratio_agent_fee' => $item['ratio_agent_fee'],
                'create_time' => $item['create_time'],
                'update_time' => $item['update_time'],
                'delete_time' => $item['delete_time'],
            ];
        }
        Db::name("agent")->insertAll($agentData);
        echo "处理代理商数据完成：【".count($agentData)."】\n";
    }
}