<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateMemberUsdt extends Migrator
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

        $table = $this->table('member');
        $table
            ->addColumn(Column::string('usdt_bep_card')->setLimit(150)->setComment('usdt bep'))
            ->addColumn(Column::string('usdt_card')->setLimit(150)->setDefault("")->setComment('usdt trx20'))
            ->update();

    }
}
