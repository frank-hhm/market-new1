<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberCard extends Migrator
{

    public function up()
    {
        $table = $this->table('member_card');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::enum('bind_status', [0,1,2,3])->setDefault('0')->setComment('认证状态'))
            ->addColumn(Column::integer('bind_time')->setLimit(11)->setDefault(0)->setComment('认证时间'))
            ->addColumn(Column::string('real_name')->setLimit(120)->setDefault('')->setComment('真实姓名'))
            ->addColumn(Column::string('card_number')->setLimit(150)->setDefault('')->setComment('身份证号'))
            ->addColumn(Column::integer('handle_time')->setLimit(11)->setDefault(0)->setComment('处理时间'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id'])
            ->setComment('用户认证记录')
            ->create();
    }
    public function down()
    {
        $table = $this->table('member_card');
    }
}
