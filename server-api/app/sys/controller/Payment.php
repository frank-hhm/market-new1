<?php
/**
 * @Date: 2025/6/27 17:49
 */
declare(strict_types=1);
namespace app\sys\controller;

use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\FileUploadCacheService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\PaymentService;

/**
 * 收款渠道
 * Class Payment
 * @package app\sys\controller
 */
class Payment extends Base
{
    /**
     * Payment constructor.
     * @param App $app
     * @param PaymentService $service
     */
    public function __construct(App $app, PaymentService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 渠道列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
        ]);
        $this->success('success',$this->service->getList($data));
    }
    /**
     * 渠道列表
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $data = $this->request->postMore([
        ]);
        $this->success('success',$this->service->getSelect($data));
    }

    /**
     * 收款渠道详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 添加收款渠道
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['name', ''],
            ['nickname', ''],
            ['cover', ''],
            ['setting', ''],
            ['min', 1],
            ['max', 1],
            ['sort', 0],
            ['status', 1],
            ['type','offline_bank'],
            ['is_qrcode',0]
        ]);
//        dump($data);die;
        if( $this->service->save($data)){
            $this->success('添加成功!');
        }
        $this->error('添加失败!');
    }

    /**
     * 编辑收款渠道
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['name', ''],
            ['nickname', ''],
            ['cover', ''],
            ['setting', ''],
            ['min', 1],
            ['max', 1],
            ['sort', 0],
            ['status', 1],
            ['type','offline_bank'],
            ['is_qrcode',0]
        ]);
        if($this->service->update($data['id'], $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除收款渠道
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
     * 修改收款渠道状态
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



    /**
     * 关联代理
     * @method(POST)
     */
    public function updateAgent()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['agent_id', ''],
        ]);
        if(is_null($data['agent_id'])){
            $this->error('请选择代理');
        }
        $msg = $data['agent_id'] == 0 ? '解除关联' : '关联代理';
        if($this->service->update($data['id'],[
            'agent_id'=>$data['agent_id']
        ])){
            $this->success($msg.'成功!');
        }
        $this->error($msg.'失败!');
    }
}