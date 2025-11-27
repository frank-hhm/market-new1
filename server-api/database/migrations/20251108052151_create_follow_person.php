<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateFollowPerson extends Migrator
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
        $table = $this->table('follow_person');
        $table

    ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn(Column::string('nickname')->setLimit(255)->setDefault('')->setComment(''))
            ->addColumn(Column::string('signature')->setLimit(255)->setDefault('')->setComment(''))
            ->addColumn(Column::string('avatar')->setLimit(255)->setDefault('')->setComment('头像'))
            ->addColumn(Column::string('deposit_text')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::string('month_profit_text')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::string('month_win_rate_text')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::string('revenue_text')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::string('total_profit_text')->setLimit(150)->setDefault('')->setComment(''))
            ->addColumn(Column::tinyInteger('status')->setLimit(3)->setDefault(1)->setComment('状态，1启用，0禁用'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))

            ->setPrimaryKey('id')
            ->addIndex(['member_id','status','id'])
            ->setComment('跟单交易员')
            ->create();
    }
}
