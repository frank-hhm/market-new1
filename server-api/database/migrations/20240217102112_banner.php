<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Banner extends Migrator
{
    public function up()
    {
        $table = $this->table('banner');
        $table
            ->addColumn(Column::string('cover')->setLimit(255)->setDefault('')->setComment('图片'))
            ->addColumn(Column::text('link')->setComment('链接'))
            ->addColumn(Column::integer('sort')->setDefault(0)->setComment('排序'))
            ->addColumn(Column::enum('status', [0,1])->setDefault('1')->setComment('1正常，0禁用'))
            ->addColumn('create_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '创建时间'))
            ->addColumn('update_time', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '更新时间'))
            ->setPrimaryKey('id')
            ->setComment('素材分组')
            ->create();
    }

    public function down()
    {
        $table = $this->table('banner');
    }
}
