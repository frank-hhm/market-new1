<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateOrderPingCangLog extends Migrator
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
        $table = $this->table('order_ping_cang_log');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('会员ID'))
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('项目ID'))
            ->addColumn(Column::integer('order_id')->setLimit(11)->setDefault(0)->setComment('项目ID'))
            ->addColumn(Column::integer('selltime')->setLimit(11)->setDefault(0)->setComment('平仓时间'))
            ->addColumn(Column::string('sellprice')->setLimit(60)->setDefault('')->setComment('平仓价格'))
            ->addColumn(Column::string('sell_type')->setLimit(60)->setDefault('')->setComment('平仓类型'))
            ->addColumn(Column::text('other_data')->setComment(''))
            ->addColumn(Column::text('user_jiao_info')->setComment(''))
            ->addColumn(Column::text('order_data')->setComment(''))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id','pid','order_id'])
            ->setComment('订单日记')
            ->create();
    }
}
