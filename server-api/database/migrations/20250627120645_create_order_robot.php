<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateOrderRobot extends Migrator
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

        $table = $this->table('order_robot');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('会员ID'))
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('项目ID'))
            ->addColumn(Column::integer('order_id')->setLimit(11)->setDefault(0)->setComment('订单ID'))
            ->addColumn(Column::string('ptitle')->setLimit(150)->setDefault('')->setComment('项目ID'))

            ->addColumn(Column::enum('ostyle',[1,2])->setDefault('1')->setComment('2涨 1跌'))
            ->addColumn(Column::integer('onumber')->setLimit(11)->setDefault(0)->setComment('手数'))
            ->addColumn(Column::enum('status',[0,1,2])->setDefault('0')->setComment('0挂单中，1已成交，3撤单'))
            ->addColumn(Column::decimal('buyprice',10,2)->setDefault(0.00)->setComment('挂单标记价'))
            ->addColumn(Column::decimal('order_price',10,2)->setDefault(0.00)->setComment('入仓保证金'))
            ->addColumn(Column::string('surplus')->setLimit(40)->setDefault('')->setComment('止盈'))
            ->addColumn(Column::string('loss')->setLimit(40)->setDefault('')->setComment('止损'))
            ->addColumn(Column::integer('deal_time')->setLimit(11)->setDefault(0)->setComment('成交 时间'))
            ->addColumn(Column::integer('close_time')->setLimit(11)->setDefault(0)->setComment('撤单时间'))
            ->addColumn(Column::text('remark')->setComment('备注'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id','pid'])
            ->setComment('挂单记录')
            ->create();
    }
}
