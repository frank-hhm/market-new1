<?php
/**
 * @Date: 2025/6/28 10:57
 */
declare(strict_types=1);


namespace app\sys\controller;

use app\common\services\common\FileUploadCacheService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\BannerService;
/**
 * 轮播图
 * Class Banner
 * @package app\sys\controller\mate
 */
class Banner extends Base
{
    /**
     * Banner constructor.
     * @param App $app
     * @param BannerService $service
     */
    public function __construct(App $app, BannerService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }



    /**
     * 轮播列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
        ]);
        $this->success($this->service->getList($data));
    }

    /**
     * 轮播详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 添加轮播
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['cover', ''],
            ['link', ''],
            ['status', 1],
            ['sort', 0]
        ]);
        if( $this->service->save($data)){
            $this->success('添加轮播图成功!');
        }
        $this->error('添加轮播图失败!');
    }

    /**
     * 编辑角色
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['cover', ''],
            ['link', ''],
            ['status', 1],
            ['sort', 0]
        ]);
        if($this->service->update($data['id'], $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除轮播
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