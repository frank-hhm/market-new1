<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderLog extends Migrator
{
    public function change()
    {
        $table = $this->table('order_log');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('产品ID'))
            ->addColumn(Column::string('ptitle')->setLimit(60)->setDefault('')->setComment('产品'))
            ->addColumn(Column::string('username')->setLimit(60)->setDefault('')->setComment('用户名'))
            ->addColumn(Column::enum('ostyle',[0,1])->setDefault('0')->setComment('0涨 1跌'))
            ->addColumn(Column::string('onumber')->setLimit(30)->setDefault("0")->setComment('手数'))
            ->addColumn(Column::string('price')->setLimit(30)->setDefault("0")->setComment('价格'))
            ->addColumn(Column::enum('type',[0,1])->setDefault('0')->setComment('0买入 1卖出'))
            ->addColumn(Column::integer('oid')->setLimit(11)->setDefault(0)->setComment('订单ID'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','pid','member_id','oid','type'])
            ->setComment('订单记录')
            ->create();
    }
}
