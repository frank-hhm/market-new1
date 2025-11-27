<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MarketOrder extends Migrator
{
    public function change()
    {
        $table = $this->table('order');
        $table
            ->addColumn(Column::integer('agent_id')->setLimit(11)->setDefault(0)->setComment('代理ID'))
            ->addColumn(Column::integer('member_id')->setLimit(11)->setDefault(0)->setComment('会员ID'))
            ->addColumn(Column::integer('pid')->setLimit(11)->setDefault(0)->setComment('项目ID'))
            ->addColumn(Column::string('ptitle')->setLimit(150)->setDefault('')->setComment('项目名称'))
            ->addColumn(Column::string('orderno')->setLimit(40)->setDefault('')->setComment('订单编号'))
            ->addColumn(Column::enum('ostyle',[0,1])->setDefault('0')->setComment('0涨 1跌'))
            ->addColumn(Column::integer('buytime')->setLimit(11)->setDefault(0)->setComment('建仓'))
            ->addColumn(Column::string('onumber')->setLimit(40)->setDefault("0")->setComment('手数'))
            ->addColumn(Column::integer('selltime')->setLimit(11)->setDefault(0)->setComment('平仓'))
            ->addColumn(Column::enum('ostaus',[0,1,2,3])->setDefault('0')->setComment('0交易，1平仓，2委托，3撤单'))
            ->addColumn(Column::string('buyprice')->setLimit(40)->setComment('入仓价'))
            ->addColumn(Column::string('surplus')->setLimit(40)->setDefault('')->setComment('止盈'))
            ->addColumn(Column::string('loss')->setLimit(40)->setDefault('')->setComment('止损'))
            ->addColumn(Column::string('sellprice')->setLimit(40)->setComment('平仓价'))
            ->addColumn(Column::decimal('endprofit',10,2)->setDefault(0.00)->setComment('点数/分钟数'))
            ->addColumn(Column::decimal('endloss',10,2)->setDefault(0.00)->setComment('盈亏比例'))
            ->addColumn(Column::decimal('fee',10,2)->setDefault(0.00)->setComment('总费用'))
            ->addColumn(Column::enum('eid',[1,2])->setDefault('1')->setComment('1点位、2时间'))
            ->addColumn(Column::decimal('commission',10,2)->setDefault(0.00)->setComment('佣金'))
            ->addColumn(Column::decimal('ploss',10,2)->setDefault(0.00)->setComment('盈亏'))
            ->addColumn(Column::enum('display',[0,1])->setDefault('0')->setComment('0,可查询，1不可查询'))
            ->addColumn(Column::enum('isshow',[0,1])->setDefault('0')->setComment(''))
            ->addColumn(Column::enum('is_win',[1,2,3])->setDefault('3')->setComment('是否盈利1盈利2亏损3无效'))
            ->addColumn(Column::enum('kong_type',[0,1,2])->setDefault('0')->setComment('0不空1盈利2亏损'))
            ->addColumn(Column::decimal('sx_fee',10,2)->setDefault(0.00)->setComment('手续费'))
            ->addColumn(Column::text('yong_members')->setComment('用户佣金'))
            ->addColumn(Column::text('yong_agents')->setComment('代理佣金'))
            ->addColumn(Column::decimal('yong_members_money',10,2)->setDefault(0.00)->setComment('总费用'))
            ->addColumn(Column::decimal('yong_agents_money',10,2)->setDefault(0.00)->setComment('总费用'))
            ->addColumn(Column::integer('yingkui')->setLimit(11)->setDefault(0)->setComment('浮动盈亏'))
            ->addColumn(Column::enum('sys_ping',[0,1,2,3])->setDefault('0')->setComment('0用户单平，1用户全平，2止盈线，3止损'))

            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->addColumn('delete_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','member_id','pid','orderno'])
            ->setComment('项目订单')
            ->create();
    }
}
