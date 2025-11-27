<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMemberCommissionWater extends Migrator
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

        $table = $this->table('member_commission_water');
        $table
            ->addColumn(Column::integer('order_id')->setLimit(11)->setDefault(0)->setComment('订单ID'))
            ->addColumn(Column::integer('people_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理商ID'))
            ->addColumn(Column::decimal('money', 10, 2)->setDefault(0.00)->setComment('变动金额'))
            ->addColumn(Column::decimal('order_price', 10, 2)->setDefault(0.00)->setComment('变动金额'))
            ->addColumn(Column::enum('status', [0,1])->setDefault('1')->setComment('0进行中，1完成'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','people_id','member_id','agent_id','order_id','status'])
            ->setComment('佣金日记')
            ->create();
    }
}
