<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateVersionData extends Migrator
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


        $table = $this->table('version_data');
        $table

            ->addColumn(Column::string('version')->setLimit(150)->setDefault('')->setComment('版本'))
            ->addColumn(Column::string('source')->setLimit(150)->setDefault('')->setComment('环境:dev'))
            ->addColumn(Column::enum('is_app',[0,1])->setDefault('0')->setComment('是否有APP更新'))
            ->addColumn(Column::enum('is_wgt',[0,1])->setDefault('0')->setComment('是否为wgt包'))
            ->addColumn(Column::string('desc')->setLimit(150)->setDefault('')->setComment('描述'))
            ->addColumn(Column::string('wgt_name')->setLimit(150)->setDefault('')->setComment('wgt包名'))
            ->addColumn(Column::string('app_name')->setLimit(150)->setDefault('')->setComment('app包名'))
            ->addColumn(Column::enum('status',[0,1,2,3,11,22])->setDefault('0')->setComment('状态'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))

            ->setPrimaryKey('id')
            ->addIndex(['id','source','is_app','is_wgt','version'])
            ->setComment('版本管理')
            ->create();
    }
}
