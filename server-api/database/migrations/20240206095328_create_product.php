<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateProduct extends Migrator
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
            ->addColumn(Column::string('name')->setLimit(80)->setDefault('')->setComment('产品名称'))
            ->addColumn(Column::string('code')->setLimit(30)->setDefault('')->setComment('显示代码'))
            ->addColumn(Column::string('real_code')->setLimit(80)->setDefault('')->setComment('代码'))
            ->addColumn(Column::string('market_source')->setLimit(80)->setDefault('')->setComment('数据来源'))
            ->addColumn(Column::integer('sector_id')->setLimit(11)->setDefault(0)->setComment('模块'))

            ->addColumn(Column::decimal('number', 14, 5)->setDefault(0.00000)->setComment('涨跌盈亏基数'))
            ->addColumn(Column::decimal('money', 14, 5)->setDefault(0.00000)->setComment('涨跌盈亏基数金额'))
            ->addColumn(Column::decimal('point_rand', 14, 5)->setDefault(0.00000)->setComment('随机波动范围'))
            ->addColumn(Column::decimal('point_min', 14, 5)->setDefault(0.00000)->setComment('波动最小值'))
            ->addColumn(Column::decimal('point_max', 14, 5)->setDefault(0.00000)->setComment('波动最大值'))

            ->addColumn(Column::decimal('fee_buy',10, 2)->setDefault(0.00)->setComment('手续费'))
            ->addColumn(Column::decimal('pay_choose',10, 2)->setDefault(0.00)->setComment('保证金'))
            ->addColumn(Column::text('open_time')->setComment('开市时间'))
            ->addColumn(Column::enum('yesterday_close', [0,1])->setDefault('1')->setComment('下单第二天强制平仓前天订单'))
            ->addColumn(Column::string('yesterday_close_time')->setLimit(80)->setDefault('')->setComment('隔夜平仓时间'))
            ->addColumn(Column::decimal('buy_min',10, 2)->setDefault(1.00)->setComment('单笔最小交易手数'))
            ->addColumn(Column::decimal('buy_max',10, 2)->setDefault(10.00)->setComment('单笔最大交易手数'))
            ->addColumn(Column::integer('decimal')->setLimit(10)->setDefault(0)->setComment('小数位数'))

            ->addColumn(Column::enum('is_hot', [0,1])->setDefault('0')->setComment('是否热门'))
            ->addColumn(Column::enum('status', [0,1])->setDefault('1')->setComment('状态'))

            ->addColumn(Column::integer('sort')->setLimit(11)->setDefault(0)->setComment('排序'))
            ->addColumn(Column::integer('create_time')->setLimit(11)->setDefault(0)->setComment('新增时间'))
            ->addColumn(Column::integer('update_time')->setLimit(11)->setDefault(0)->setComment('更新时间'))
            ->addColumn(Column::integer('delete_time')->setLimit(11)->setDefault(0)->setComment('删除时间'))
            ->setPrimaryKey('id')
            ->addIndex(['id','sector_id'])
            ->setComment('项目')
            ->create();
    }
}
