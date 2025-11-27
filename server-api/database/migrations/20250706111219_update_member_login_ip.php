<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateMemberLoginIp extends Migrator
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
            ->addColumn('last_ip', 'string', array('limit' => 16, 'default' => '', 'comment' => '最后一次登录ip'))
            ->addColumn('last_time', 'integer', array('limit' => 11, 'default' =>0, 'comment' => '最后一次登录时间'))
            ->addColumn('login_count', 'integer', array('limit' => 11, 'default' =>0, 'comment' => '登录次数'))
            ->update();

    }
}
