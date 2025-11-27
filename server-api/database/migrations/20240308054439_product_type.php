<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ProductType extends Migrator
{
    public function change()
    {
        $table = $this->table('product_type');
        $table
            ->addColumn(Column::string('name')->setLimit(11)->setDefault(0)->setComment('产品名称'))
            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('排序'))
            ->addColumn(Column::enum('status', [0,1])->setDefault('0')->setComment('状态'))
            ->setPrimaryKey('id')
            ->addIndex(['id','name'])
            ->setComment('产品分类')
            ->create();
    }
}
