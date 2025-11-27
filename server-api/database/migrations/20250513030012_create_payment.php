<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreatePayment extends Migrator
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

        $table = $this->table('payment');
        $table
            ->addColumn(Column::string('name')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::string('nickname')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::tinyInteger('min')->setLimit(1)->setDefault(0)->setComment(''))
            ->addColumn(Column::tinyInteger('max')->setLimit(1)->setDefault(0)->setComment(''))
            ->addColumn(Column::string('cover')->setLimit(255)->setDefault('')->setComment(''))
            ->addColumn(Column::string('type')->setLimit(50)->setDefault('')->setComment(''))
            ->addColumn(Column::string('setting')->setLimit(1000)->setDefault('')->setComment('配置项'))
            ->addColumn(Column::enum('status', [0,1])->setDefault('0')->setComment('状态'))
            ->addColumn(Column::tinyInteger('sort')->setLimit(1)->setDefault(0)->setComment(''))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id'])
            ->setComment('收款渠道')
            ->create();

    }
}
