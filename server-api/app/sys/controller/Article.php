<?php
/**
 * @Date: 2025/6/30 17:56
 */
declare(strict_types=1);
namespace app\sys\controller;

use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use app\common\services\common\FileUploadCacheService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\ArticleService;

/**
 * 文章
 * Class Article
 * @package app\sys\controller
 */
class Article extends Base
{
    /**
     * Article constructor.
     * @param App $app
     * @param ArticleService $service
     */
    public function __construct(App $app, ArticleService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 文章列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
        ]);
        $this->success($this->service->getList($data));
    }

    /**
     * 文章详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 添加文章
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['cover', ''],
            ['title', ''],
            ['title_desc', ''],
            ['content', ''],
            ['status', 1],
        ]);
        if( $this->service->save($data)){
            $this->success('添加文章成功!');
        }
        $this->error('添加文章失败!');
    }

    /**
     * 编辑文章
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['cover', ''],
            ['title', ''],
            ['title_desc', ''],
            ['content', ''],
            ['status', 1],
        ]);
        if($this->service->update($data['id'], $data)){
            $this->success('修改成功!');
        }
        $this->error('修改失败!');
    }

    /**
     * 删除文章
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
     * 修改文章状态
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
     * 采集
     * @method(GET)
     */
    public function gather(){

        if($res = $this->service->gather()){
            $this->success('执行成功');
        }
        $this->error($this->service->getError()?:'执行失败');
    }
}