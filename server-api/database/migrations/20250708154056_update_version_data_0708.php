<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateVersionData0708 extends Migrator
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

        $table = $this->table('version_data');
        $table
            ->addColumn(Column::string('wgt_uat_name')->setLimit(150)->setDefault('')->setComment('wgt包名'))
            ->addColumn(Column::string('app_uat_name')->setLimit(150)->setDefault('')->setComment('app包名'))
            ->update();
    }
}
