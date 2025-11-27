<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberCoin extends Migrator
{

    public function up()
    {
        $table = $this->table('member_coin');
        $table
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::decimal('balance', 10, 2)->setDefault(0.00)->setComment('余额'))

            ->addColumn(Column::decimal('yingkui', 10, 2)->setDefault(0.00)->setComment('期货盈亏'))
            ->addColumn(Column::decimal('keyong', 10, 2)->setDefault(0.00)->setComment('期货可用'))
            ->addColumn(Column::decimal('jingzhi', 10, 2)->setDefault(0.00)->setComment('期货净值'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id'])
            ->setComment('用户钱包')
            ->create();
    }

    public function down()
    {
        $table = $this->table('member_coin');
    }
}
