<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AddProductTypeData extends Migrator
{

    public function change()
    {
        Db::name('product_type')->insertAll(
            [
                [
                    "id"=>1,
                    "name"=>'国际期货',
                    'sort'=>800,
                    'status'=>1
                ],
                [
                    "id"=>2,
                    "name"=>'国内期货',
                    'sort'=>1000,
                    'status'=>1
                ],
                [
                    "id"=>3,
                    "name"=>'指数',
                    'sort'=>800,
                    'status'=>1
                ],
                [
                    "id"=>4,
                    "name"=>'数字货币',
                    'sort'=>800,
                    'status'=>1
                ]
            ]);
    }
}
