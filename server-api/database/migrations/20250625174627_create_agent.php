<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAgent extends Migrator
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
        $table = $this->table('agent');
        $table
            ->addColumn('account', 'string', array('limit' => 60, 'default' => '', 'comment' => '账号'))
            ->addColumn('avatar', 'string', array('limit' => 255, 'default' => '', 'comment' => '头像'))
            ->addColumn('pwd', 'string', array('limit' => 100, 'default' => '', 'comment' => '密码'))
            ->addColumn('real_name', 'string', array('limit' => 60, 'default' => '', 'comment' => '姓名'))
            ->addColumn('roles', 'string', array('limit' => 128, 'default' => '', 'comment' => '权限'))
            ->addColumn('last_ip', 'string', array('limit' => 16, 'default' => '', 'comment' => '最后一次登录ip'))
            ->addColumn('last_time', 'integer', array('limit' => 11, 'default' =>0, 'comment' => '最后一次登录时间'))
            ->addColumn('login_count', 'integer', array('limit' => 11, 'default' =>0, 'comment' => '登录次数'))
            ->addColumn(Column::tinyInteger('level')->setLimit(3)->setDefault(1)->setComment(''))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(1)->setComment('状态，1启用，0禁用'))

            ->addColumn(Column::tinyInteger('pid')->setLimit(11)->setDefault(0)->setComment('上级'))
            ->addColumn(Column::string('p_nickname')->setLimit(80)->setDefault('')->setComment('上级名称'))
            ->addColumn(Column::decimal('ratio', 10, 2)->setDefault(0.00)->setComment('盈利分配比例'))
            ->addColumn(Column::decimal('ratio_fee', 10, 2)->setDefault(0.00)->setComment('手续费加成'))
            ->addColumn(Column::decimal('balance', 20, 8)->setDefault(0.00000000)->setComment('余额'))
            ->addColumn(Column::string('invite_code')->setLimit(30)->setDefault('')->setComment('邀请码'))
            ->addColumn(Column::text('ratio_agent_fee')->setComment(''))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['account'], ['unique' => true])
            ->addIndex(['status','pid','invite_code'])
            ->setComment('代理商表')
            ->create();
    }
}
