<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateProduct0705 extends Migrator
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
        $table = $this->table('product');
        $table
            ->addColumn(Column::string('buy_slippage')->setLimit(60)->setDefault('')->setComment('买仓滑点'))
            ->addColumn(Column::string('sell_slippage')->setLimit(60)->setDefault('')->setComment('卖仓滑点'))
            ->update();
    }
}
