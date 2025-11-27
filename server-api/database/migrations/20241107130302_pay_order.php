<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PayOrder extends Migrator
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

        $table = $this->table('pay_order');
        $table
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户id'))
            ->addColumn(Column::string('order_no')->setLimit(50)->setDefault('')->setComment('订单号'))
            ->addColumn(Column::decimal('pay_price', 10, 2)->setDefault(0.00)->setComment('支付'))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(0)->setComment('状态'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id','order_no','status'])
            ->setComment('支付表')
            ->create();
    }
}
