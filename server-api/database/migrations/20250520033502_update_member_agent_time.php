<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateMemberAgentTime extends Migrator
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
        $this->table('member')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('finance_member_fee_cash_water')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('finance_member_recharge')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('finance_member_withdrawal')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('finance_water')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('member_card')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('member_commission_water')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('order')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('order_log')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();
        $this->table('turntable_record_log')->addColumn(Column::integer('update_agent_time')->setDefault(0)->setComment('转移代理时间'))
            ->update();

    }
}
