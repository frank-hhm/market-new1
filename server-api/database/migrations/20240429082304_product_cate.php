<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ProductCate extends Migrator
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
        $table = $this->table('product_cate');
        $table
            ->addColumn(Column::string('cate_name')->setLimit(150)->setDefault('')->setComment('板块名称'))
            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('排序'))
            ->setPrimaryKey('id')
            ->addIndex(['id','cate_name'])
            ->setComment('产品板块')
            ->create();

    }
}
