<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateFinanceMemberFeeCashWater extends Migrator
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

        $table = $this->table('finance_member_fee_cash_water');
        $table
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理商ID'))

            ->addColumn(Column::decimal('money', 10, 2)->setDefault(0.00)->setComment('金额'))
            ->addColumn(Column::decimal('fee', 10, 2)->setDefault(0.00)->setComment('手续费'))
            ->addColumn('describe', 'string', array('limit' => 255, 'default' => '', 'comment' => '描述/说明'))
            ->addColumn(Column::integer('source_id')->setLimit(11)->setDefault(0)->setComment('来源id'))
            ->addColumn(Column::string('source')->setLimit(30)->setDefault('recharge')->setComment('来源(order)'))
            ->addColumn(Column::enum('settlement_status', [0,1])->setDefault('0')->setComment('是否结算'))
            ->addColumn('settlement_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '结算时间'))
            ->addColumn('order_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '订单下单时间'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->setComment('用户手续费返现记录')
            ->create();
    }
}
