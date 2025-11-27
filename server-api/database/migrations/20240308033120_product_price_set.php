<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ProductPriceSet extends Migrator
{
    public function change()
    {
        $table = $this->table('product_price_set');
        $table
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('产品ID'))
            ->addColumn(Column::decimal('Price', 10, 2)->setDefault(0.00)->setComment('金额'))
            ->addColumn(Column::integer('execute_time')->setDefault(0)->setComment('执行时间'))
            ->addColumn(Column::integer('complete_time')->setDefault(0)->setComment('结束时间'))
            ->addColumn(Column::text('content')->setComment('执行结果'))
            ->addColumn(Column::enum('status', ['success','failure'])->setDefault('failure')->setComment('状态'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','pid','execute_time','complete_time','status'])
            ->setComment('产品调控任务日志表')
            ->create();
    }
}
