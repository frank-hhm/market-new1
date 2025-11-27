<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AddSystemMenusData1 extends Migrator
{
    public function change()
    {

        Db::name('system_menus')->insertAll(
            [
                [
                    "id"=>89,
                    "pid"=>53,
                    "module"=>1,
                    "icon"=>"",
                    "menu_name"=>"平仓",
                    "menu_path"=>"",
                    "menu_node"=>"product-order-sell",
                    "api_rule"=>"sys/order/pingSell",
                    "params"=>"",
                    "sort"=>0,
                    "type"=>2,
                    "status"=>1,
                    "create_time"=>0,
                    "update_time"=>1708506903,
                    "delete_time"=>0
                ]
            ]);
    }
}
