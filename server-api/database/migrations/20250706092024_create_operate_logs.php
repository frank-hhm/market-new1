<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateOperateLogs extends Migrator
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

        $table = $this->table('operate_logs');
        $table
            ->addColumn(Column::integer('source_id')->setLimit(11)->setDefault(0)->setComment('来源'))
            ->addColumn(Column::string('source')->setLimit(60)->setDefault('')->setComment('来源：admin,agent,member'))


            ->addColumn(Column::string('ip')->setLimit(60)->setDefault('')->setComment('操作ip'))
            ->addColumn(Column::text('user_agent')->setComment('用户代理'))
            ->addColumn(Column::text('param_data')->setComment('提交数据'))
            ->addColumn(Column::string('method')->setLimit(150)->setDefault('')->setComment('提交方式'))
            ->addColumn(Column::string('type')->setLimit(150)->setDefault('')->setComment('操作类型:login'))

            ->addColumn(Column::string('message')->setLimit(150)->setDefault('')->setComment('错误信息'))
            ->addColumn(Column::string('content')->setLimit(150)->setDefault('')->setComment('信息'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))

            ->setPrimaryKey('id')
            ->addIndex(['source_id','id','ip','source','type','method'])
            ->setComment('操作日记')
            ->create();
    }
}
