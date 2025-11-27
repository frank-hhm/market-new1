<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class UpdateSystemRole1 extends Migrator
{


    public function change()
    {
        Db::name('system_role')->where('id',1)->update([
            'rules' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,41,51,58,43,42,59,57,60,56,61,38,39,83,82,81,80,40,85,84,32,55,54,53,52,76,77,78,79,44,36,33,74,73,72,71,34,75,31,30,29,62,63,64,65,66,70,69,67,68,46,37,35,50,49,48,47,45,87,88,89',
        ]);
    }
}
