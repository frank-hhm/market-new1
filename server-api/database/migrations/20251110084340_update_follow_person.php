<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UpdateFollowPerson extends Migrator
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

        $table = $this->table('follow_person');
        $table
            ->addColumn(Column::string('revenue_type')->setLimit(30)->setDefault('auto')->setComment(''))
            ->addColumn(Column::decimal('revenue_min',10,2)->setDefault('0.00')->setComment('随机小'))
            ->addColumn(Column::decimal('revenue_max',10,2)->setDefault('0.00')->setComment('随机大'))
            ->addColumn(Column::decimal('revenue_lock',10,2)->setDefault('0.00')->setComment('固定值'))
            ->addColumn(Column::decimal('commission1',10,2)->setDefault('0.00')->setComment('一级'))
            ->addColumn(Column::decimal('commission2',10,2)->setDefault('0.00')->setComment('二级'))
            ->addColumn(Column::integer('default_create_day')->setLimit(11)->setDefault(0)->setComment('天数'))

            ->addColumn(Column::integer('follow_count_text')->setLimit(11)->setDefault(0)->setComment('跟单人数'))
            ->addColumn(Column::enum('is_show_order',['0','1'])->setDefault('0')->setComment('是否显示跟单订单'))
            ->addIndex(['revenue_type'])
            ->update();
    }
}
