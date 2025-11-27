<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateMarketKLine extends Migrator
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
        $table = $this->table('market_k_line');
        $table
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('项目ID'))
            ->addColumn(Column::string('open_price')->setLimit(18)->setDefault(0.00)->setComment('开盘价'))
            ->addColumn(Column::string('height_price')->setLimit(18)->setDefault(0.00)->setComment('最高值'))
            ->addColumn(Column::string('low_price')->setLimit(18)->setDefault(0.00)->setComment('最低值'))
            ->addColumn(Column::string('close_price')->setLimit(18)->setDefault(0.00)->setComment('关盘价格'))
            ->addColumn(Column::integer('type')->setLimit(11)->setDefault(0)->setComment(''))
            ->addColumn(Column::integer('volume')->setLimit(11)->setDefault(0)->setComment('量'))
            ->addColumn(Column::string('ktime')->setLimit(18)->setDefault('')->setComment('k线时间'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','pid','type','ktime'])
            ->setComment('产品k线')
            ->create();
    }
}
