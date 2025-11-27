<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Member extends Migrator
{
    public function up()
    {
        $table = $this->table('member');
        $table
            ->addColumn('username', 'string', array('limit' => 150, 'default' => '', 'comment' => '用户账号'))
            ->addColumn('nickname', 'string', array('limit' => 150, 'default' => '', 'comment' => '用户昵称'))
            ->addColumn('mobile', 'string', array('limit' => 120, 'default' => '', 'comment' => '手机号码'))
            ->addColumn('avatar', 'string', array('limit' => 255, 'default' => '', 'comment' => '用户头像'))
            ->addColumn('password', 'string', array('limit' => 100, 'default' => '', 'comment' => '密码'))
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('people')->setLimit(11)->setDefault(0)->setComment('推广用户'))
            ->addColumn(Column::integer('people_tui')->setLimit(11)->setDefault(0)->setComment('伞下用户'))
            ->addColumn(Column::string('invite_code')->setLimit(20)->setDefault('')->setComment('推广码'))

            ->addColumn(Column::enum('moni', [0,1])->setDefault('0')->setComment('是否模拟账号'))
            ->addColumn(Column::integer('moni_id')->setLimit(11)->setDefault(0)->setComment('模拟用户id'))
            ->addColumn(Column::enum('bind_status', [0,1,2,3])->setDefault('0')->setComment('认证状态'))
            ->addColumn(Column::integer('bind_time')->setLimit(11)->setDefault(0)->setComment('认证时间'))
            ->addColumn(Column::string('real_name')->setLimit(120)->setDefault('')->setComment('真实姓名'))
            ->addColumn(Column::string('card_number')->setLimit(150)->setDefault('')->setComment('身份证号'))
            ->addColumn(Column::string('bank_child')->setLimit(120)->setDefault('')->setComment('开户支行'))
            ->addColumn(Column::string('bank_code')->setLimit(120)->setDefault('')->setComment('银行卡号'))
            ->addColumn(Column::string('bank_name')->setLimit(120)->setDefault('')->setComment('银行'))
            ->addColumn(Column::string('bank_real_name')->setLimit(80)->setDefault('')->setComment('持卡人'))


            ->addColumn(Column::string('register_source')->setLimit(30)->setDefault('h5')->setComment('注册来源'))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(1)->setComment('用户状态'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['mobile','username'], ['unique' => true])
            ->setComment('用户')
            ->create();
    }
    public function down()
    {
        $table = $this->table('member');
    }
}
