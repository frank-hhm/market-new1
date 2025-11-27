<?php
/**
 * @Date: 2025/1/10 15:18
 */
declare(strict_types=1);

namespace app\sys\controller;

use app\common\services\common\FileUploadCacheService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\ProductSectorService;
/**
 * 产品板块
 * Class ProductType
 * @package app\sys\controller\mate
 */
class ProductSector extends Base
{
    /**
     * ProductSector constructor.
     * @param App $app
     * @param ProductSectorService $service
     */
    public function __construct(App $app, ProductSectorService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 添加产品板块
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['sector_name', ''],
            ['sort', 0],
        ]);
        if ($this->service->create($data)) {
            $this->success('添加成功!');
        }
        $this->error($this->service->getError()?:'添加失败!');
    }

    /**
     * 编辑产品板块
     * @method(POST)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', 0],
            ['sector_name', ''],
            ['sort', 0],
        ]);
        if ($this->service->update($data['id'],$data)) {
            $this->success('编辑成功!');
        }
        $this->error('编辑失败!');
    }
    /**
     * 删除产品板块
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
     * 获取产品板块数据
     * @method(GET)
     */
    public function select()
    {
        $this->success('获取成功',$this->service->getSelect());
    }
}