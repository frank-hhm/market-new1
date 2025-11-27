<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AddDataSystemMenus extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        Db::name('system_menus')->insertAll(
            [
                [
                    "id"=>99,
                    "pid"=>46,
                    "module"=>1,
                    "icon"=>"",
                    "menu_name"=>"收款渠道",
                    "menu_path"=>"/system/payment/list",
                    "menu_node"=>"system-payment-list",
                    "api_rule"=>"sys/payment/list",
                    "params"=>"",
                    "sort"=>0,
                    "type"=>1,
                    "status"=>1,
                    "create_time"=>0,
                    "update_time"=>1708506903,
                    "delete_time"=>0
                ], [
                "id"=>100,
                "pid"=>99,
                "module"=>1,
                "icon"=>"",
                "menu_name"=>"新增渠道",
                "menu_path"=>"",
                "menu_node"=>"payment-create",
                "api_rule"=>"sys/payment/create",
                "params"=>"",
                "sort"=>0,
                "type"=>2,
                "status"=>1,
                "create_time"=>0,
                "update_time"=>1708506903,
                "delete_time"=>0
            ], [
                "id"=>101,
                "pid"=>99,
                "module"=>1,
                "icon"=>"",
                "menu_name"=>"编辑渠道",
                "menu_path"=>"",
                "menu_node"=>"payment-update",
                "api_rule"=>"sys/payment/update",
                "params"=>"",
                "sort"=>0,
                "type"=>2,
                "status"=>1,
                "create_time"=>0,
                "update_time"=>1708506903,
                "delete_time"=>0
            ], [
                "id"=>102,
                "pid"=>99,
                "module"=>1,
                "icon"=>"",
                "menu_name"=>"修改状态",
                "menu_path"=>"",
                "menu_node"=>"payment-status",
                "api_rule"=>"sys/payment/status",
                "params"=>"",
                "sort"=>0,
                "type"=>2,
                "status"=>1,
                "create_time"=>0,
                "update_time"=>1708506903,
                "delete_time"=>0
            ], [
                "id"=>103,
                "pid"=>99,
                "module"=>1,
                "icon"=>"",
                "menu_name"=>"删除渠道",
                "menu_path"=>"",
                "menu_node"=>"payment-delete",
                "api_rule"=>"sys/payment/delete",
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
