<?php

use think\migration\Migrator;
use think\migration\db\Column;

class FinanceAgentWithdrawal extends Migrator
{

    public function change()
    {
        $table = $this->table('finance_agent_withdrawal');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::string('bank_name')->setLimit(120)->setDefault('')->setComment('提现银行卡名称 支行'))
            ->addColumn(Column::string('card_number')->setLimit(120)->setDefault('')->setComment('提现银行卡号'))
            ->addColumn(Column::string('real_name')->setLimit(120)->setDefault('')->setComment('银行卡姓名'))
            ->addColumn(Column::decimal('money', 10, 2)->setDefault(0.00)->setComment('提现金额'))
            ->addColumn(Column::decimal('amount', 10, 2)->setDefault(0.00)->setComment('到账金额（去除手续费）'))
            ->addColumn(Column::decimal('fee', 10, 2)->setDefault(0.00)->setComment('手续费'))
            ->addColumn('describe', 'string', array('limit' => 255, 'default' => '', 'comment' => '描述/说明'))
            ->addColumn('remark', 'string', array('limit' => 255, 'default' => '', 'comment' => '备注'))
            ->addColumn(Column::enum('status', [0,1,2])->setDefault('0')->setComment('状态，0待审核 1审核通过 2提现驳回'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','agent_id','status'])
            ->setComment('提现申请')
            ->create();
    }
}
