<?php

use think\migration\Migrator;
use think\migration\db\Column;

class FinanceWater extends Migrator
{

    public function up()
    {
        $table = $this->table('finance_water');
        $table
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('用户ID'))
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理商ID'))
            ->addColumn(Column::decimal('money', 10, 2)->setDefault(0.00)->setComment('变动金额'))
            ->addColumn('describe', 'string', array('limit' => 255, 'default' => '', 'comment' => '描述/说明'))
            ->addColumn('remark', 'string', array('limit' => 255, 'default' => '', 'comment' => '备注'))
            ->addColumn(Column::integer('source_id')->setLimit(11)->setDefault(0)->setComment('来源id'))
            ->addColumn(Column::string('source')->setLimit(30)->setDefault('recharge')->setComment('来源(recharge用户充值  admin管理员操作)'))
            ->addColumn(Column::string('pay_type')->setLimit(30)->setDefault('')->setComment('交易通道'))
            ->addColumn(Column::decimal('member_balance', 10, 2)->setDefault(0.00)->setComment('前账户余额（变动前）'))
            ->addColumn(Column::decimal('balance', 10, 2)->setDefault(0.00)->setComment('账户余额（变动后）'))
            ->addColumn(Column::enum('type', [0,1])->setDefault('1')->setComment('0减少，1增加'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id','agent_id','source','source_id','pay_type'])
            ->setComment('财务日记')
            ->create();
    }

    public function down()
    {
        $table = $this->table('finance_water');
    }
}
