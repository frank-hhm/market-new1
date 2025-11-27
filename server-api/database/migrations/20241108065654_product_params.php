<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ProductParams extends Migrator
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
        $table = $this->table('product_params');
        $table
            ->addColumn(Column::integer('pid')->setDefault(0)->setComment('产品id'))
            ->addColumn(Column::string('heyue_danwei')->setLimit(150)->setDefault('')->setComment('合约单位'))
            ->addColumn(Column::string('money_danwei')->setLimit(150)->setDefault('')->setComment('货币单位'))
            ->addColumn(Column::string('dian_cha')->setLimit(150)->setDefault('')->setComment('点差'))
            ->addColumn(Column::string('baozhengjin_rate')->setLimit(150)->setDefault('')->setComment('强制保证金'))
            ->addColumn(Column::string('geye_baozhengjin_rate')->setLimit(150)->setDefault('')->setComment('隔夜保证金'))
            ->setPrimaryKey('id')

            ->setComment('产品参数')
            ->create();
    }
}
