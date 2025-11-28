<?php
/**
 * @Date: 2025/6/28 10:57
 */
declare(strict_types=1);


namespace app\sys\controller\follow;

use app\sys\controller\Base;
use think\facade\App;
use app\common\services\follow\PersonService;
/**
 * 交易人
 * Class Person
 * @package app\sys\controller\follow
 */
class Person extends Base
{
    /**
     * Person constructor.
     * @param App $app
     * @param PersonService $service
     */
    public function __construct(App $app, PersonService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }



    /**
     * 交易人列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
        ]);
        $this->success("获取成功",$this->service->getList($data));
    }

    /**
     * 交易人详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success("获取成功",$this->service->getDetail($this->request->get('id')));
    }

    /**
     * 添加交易人
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['avatar', ''],
            ['nickname', ''],
            ['signature', ''],
            ['deposit_text', ''],
            ['month_profit_text', ''],
            ['month_win_rate_text', ''],
            ['revenue_text', ''],
            ['total_profit_text', ''],
            ['status', 1],
            ["member_id",0],
            ["commission1",0],
            ["commission2",0],
            ["revenue_type","auto"],
            ["revenue_lock",0],
            ["revenue_min",0],
            ["revenue_max",0],
            ["revenue_lock",0],
            ["follow_count_text",0],
            ["is_show_order",1],
            ["default_create_day",0]
        ]);
        if( $this->service->save($data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑交易人
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['avatar', ''],
            ['nickname', ''],
            ['signature', ''],
            ['deposit_text', ''],
            ['month_profit_text', ''],
            ['month_win_rate_text', ''],
            ['revenue_text', ''],
            ['total_profit_text', ''],
            ['status', 1],
            ["member_id",0],
            ["commission1",0],
            ["commission2",0],
            ["revenue_type","auto"],
            ["revenue_lock",0],
            ["revenue_min",0],
            ["revenue_max",0],
            ["revenue_lock",0],
            ["follow_count_text",0],
            ["is_show_order",1],
            ["default_create_day",0]
        ]);
        if($this->service->update($data['id'], $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除交易人
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }

        if ($this->service->delete((int)$id)) {
            $this->success('删除成功!');
        }
        $this->error('删除失败!');
    }

    /**
     * 修改轮播状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        if ($this->service->update($id, ['status' => $this->request->param('status')])) {
            $this->success('修改成功');
        }
        $this->error('修改失败');
    }

}