<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateMember260328 extends Migrator
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
            ->addColumn(Column::enum('withdraw_prompt',[0,1])->setDefault('0')->setComment('提现提示'))
            ->addColumn(Column::text('withdraw_prompt_text')->setComment('提现提示内容'))
            ->update();

    }
}
