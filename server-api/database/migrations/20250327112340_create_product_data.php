<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProductData extends Migrator
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
        $table = $this->table('product_data');
        $table
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('项目id'))
            ->addColumn(Column::string('price')->setLimit(18)->setDefault('0.00')->setComment('价格'))
            ->addColumn(Column::string('open_price')->setLimit(18)->setDefault('0.00')->setComment('开盘价格'))
            ->addColumn(Column::string('close_price')->setLimit(18)->setDefault('0.00')->setComment('收盘价格'))
            ->addColumn(Column::string('height_price')->setLimit(18)->setDefault('0.00')->setComment('最高'))
            ->addColumn(Column::string('low_price')->setLimit(18)->setDefault('0.00')->setComment('最低'))
            ->addColumn(Column::string('diff')->setLimit(18)->setDefault('0.00')->setComment('振幅'))
            ->addColumn(Column::string('diff_rate')->setLimit(18)->setDefault('0.00')->setComment('波幅'))
            ->addColumn(Column::string('last_close')->setLimit(18)->setDefault('0.00')->setComment('昨收'))
            ->addColumn(Column::string('volume')->setLimit(18)->setDefault('0')->setComment(''))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','pid'])
            ->setComment('产品数据表')
            ->create();
    }
}
