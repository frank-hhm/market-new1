<?php
/**
 * @Date: 2025/7/13 23:01
 */
declare(strict_types=1);

namespace app\sys\controller;

use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\FileUploadCacheService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\NoticeService;

/**
 * 公告
 * Class Notice
 * @package app\sys\controller
 */
class Notice extends Base
{
    /**
     * Notice constructor.
     * @param App $app
     * @param NoticeService $service
     */
    public function __construct(App $app, NoticeService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 公告列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
        ]);
        $this->success($this->service->getList($data));
    }

    /**
     * 公告详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 添加公告
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['title', ''],
            ['content', ''],
            ['status', 1],
        ]);
        if( $this->service->save($data)){
            $this->success('添加公告成功!');
        }
        $this->error('添加公告失败!');
    }

    /**
     * 编辑公告
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['title', ''],
            ['content', ''],
            ['status', 1],
        ]);
        if($this->service->update($data['id'], $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除公告
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
     * 修改文公告状态
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