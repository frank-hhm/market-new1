<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateFollowOrder extends Migrator
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

        $table = $this->table('follow_order');
        $table

            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn(Column::integer('person_id')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn(Column::decimal('money',10,2)->setDefault('0.00')->setComment('跟单金额'))
            ->addColumn(Column::decimal('total_revenue',10,2)->setDefault('0.00')->setComment('累计收益'))
            ->addColumn(Column::decimal('yesterday_revenue',10,2)->setDefault('0.00')->setComment('昨天收益'))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(1)->setComment('状态，1正常，0禁用，2结束'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))

            ->setPrimaryKey('id')
            ->addIndex(['member_id','person_id','status','id'])
            ->setComment('跟单订单')
            ->create();
    }
}
