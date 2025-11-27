<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AddSystemRoles extends Migrator
{
    public function change()
    {
        Db::name('system_role')->insertAll([
            [
                'id'=>1,
                'role_name' => '管理员',
                'remarks' => '管理员',
                'rules' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,41,51,58,43,42,59,57,60,56,61,38,39,83,82,81,80,40,85,84,32,55,54,53,52,76,77,78,79,44,36,33,74,73,72,71,34,75,31,30,29,62,63,64,65,66,70,69,67,68,46,37,35,50,49,48,47,45',
                'create_time' => time(),
            ],
            [
                'id'=>2,
                'role_name' => '一级代理商',
                'remarks' => '一级代理商',
                'rules' => '41,51,43,42,38,39,36,52,53,54,55,32,33,34,83,81,75,71,72,74,57',
                'create_time' => time(),
            ],
            [
                'id'=>3,
                'role_name' => '二级代理商',
                'remarks' => '二级代理商',
                'rules' => '41,51,43,42,38,39,36,52,53,54,55,32,33,34,57,56,83,81,80,74,72,71,75',
                'create_time' => time(),
            ],
            [
                'id'=>4,
                'role_name' => '三级代理商',
                'remarks' => '三级代理商',
                'rules' => '51,41,43,39,40,38,36,52,32,34,75,57,42',
                'create_time' => time(),
            ]
        ]);
    }
}
