<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMemberSubscribe extends Migrator
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

        $table = $this->table('member_subscribe');
        $table

            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn(Column::string('source')->setLimit(30)->setDefault("")->setComment(''))
            ->addColumn(Column::integer('source_id')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))

            ->setPrimaryKey('id')
            ->addIndex(['member_id','source','source_id'])
            ->setComment('用户订阅跟单员')
            ->create();
    }
}
