<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AddSystemMenusData extends Migrator
{

    public function change()
    {
        Db::name('system_menus')->insertAll(
            [
                [
                    "id"=>87,
                    "pid"=>36,
                    "module"=>1,
                    "icon"=>"",
                    "menu_name"=>"产品分类",
                    "menu_path"=>"/product/type",
                    "menu_node"=>"product-type",
                    "api_rule"=>"sys/product_type/list",
                    "params"=>"",
                    "sort"=>998,
                    "type"=>1,
                    "status"=>1,
                    "create_time"=>0,
                    "update_time"=>1708506903,
                    "delete_time"=>0
                ],
                [
                    "id"=>88,
                    "pid"=>87,
                    "module"=>1,
                    "icon"=>"",
                    "menu_name"=>"修改状态",
                    "menu_path"=>"",
                    "menu_node"=>"product-type-status",
                    "api_rule"=>"sys/product_type/status",
                    "params"=>"",
                    "sort"=>0,
                    "type"=>2,
                    "status"=>1,
                    "create_time"=>0,
                    "update_time"=>1709007426,
                    "delete_time"=>0
                ],
            ]);
    }
}
