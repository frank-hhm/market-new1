<?php
/**
 * @Date: 2025/2/6 15:15
 */
declare(strict_types=1);
namespace app\sys\controller;

use app\common\helper\ArrayHelper;
use app\common\helper\ProductHelper;
use app\common\services\ProductCacheService;
use app\common\services\ProductParamsService;
use app\sys\controller\Base;
use think\facade\App;
use app\common\services\ProductService;
use think\facade\Db;

/**
 * 产品
 * Class Product
 * @package app\sys\controller
 */
class Product extends Base
{
    /**
     * Product constructor.
     * @param App $app
     * @param ProductService $service
     */
    public function __construct(App $app, ProductService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 产品列表
     * @method(GET)
     */
    public function list()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['status',''],
            ['cate_id',''],
        ]);
        $this->success($this->service->getProductList($data));
    }

    /**
     * 添加产品
     * @method(POST)
     */
    public function create()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['sector_id',0],
            ['code', ''],
            ['real_code', ''],
            ['yesterday_close', 0],
            ['yesterday_close_time',""],
            ['open_time',[]],
            ['pay_choose',0],
            ['money', 0],
            ['number', 0],
            ['point_min', 0],
            ['point_max', 0],
            ['point_rand', 0],
            ['fee_buy', 0],
            ['pay_choose', 0],
            ['sort',0],
            ['buy_max',0],
            ['buy_min',0],
            ['decimal',0],
            ['market_source','auto'],
            ['init_price',0],
            ['product_params',[]],
            ['buy_slippage',0],
            ['sell_slippage',0],
            ["spread",0],
            ["leverage",0]
        ]);
        if(  $this->service->createProduct($data)){
            $this->success('添加产品成功!');
        }
        $this->error($this->service->getError()?:'添加产品失败!');
    }

    /**
     * 编辑产品
     * @method(PUT)
     */
    public function update()
    {
        $data = $this->request->postMore([
            ['id', ''],
            ['name', ''],
            ['sector_id',0],
            ['code', ''],
            ['real_code', ''],
            ['yesterday_close', 0],
            ['yesterday_close_time',""],
            ['open_time',[]],
            ['pay_choose',0],
            ['money', 0],
            ['number', 0],
            ['point_min', 0],
            ['point_max', 0],
            ['point_rand', 0],
            ['fee_buy', 0],
            ['pay_choose', 0],
            ['sort',0],
            ['buy_max',0],
            ['buy_min',0],
            ['decimal',0],
            ['market_source','auto'],
            ['init_price',0],
            ['product_params',[]],
            ['buy_slippage',0],
            ['sell_slippage',0],
            ["spread",0],
            ["leverage",0]
        ]);
        if( $this->service->createProduct($data)){
            $this->success('编辑成功!');
        }
        $this->error($this->service->getError()?:'编辑失败!');
    }
    /**
     * 删除产品
     * @method(DELETE)
     */
    public function delete()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        Db::startTrans();
        try{
            $res[] = $this->service->destroy((int)$id);
            $res[] =  app(ProductCacheService::class)->deleteAllCache($id);
            if (!empty($res) && ArrayHelper::checkArr($res)) {
                Db::commit();
            }
            Db::rollback();
            $this->success('删除成功');
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage()?:'删除失败');
        }
    }


    /**
     * 产品详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $id = $this->request->get('id');
//        dump(app(ProductCacheService::class)->getProductCacheSelect());die;
        $data['product'] = $this->service->getDetail($id);
        $data['product_params'] = app(ProductParamsService::class)->getDetail($id);
        $this->success('success',$data);
    }

    /**
     * 修改产品状态
     * @method(PUT)
     */
    public function status()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        Db::startTrans();
        $res[] = $this->service->update($id, ['status' => $this->request->param('status')]);
        $res[] = app(ProductCacheService::class)->deleteAllCache($id);
        if (!empty($res) && ArrayHelper::checkArr($res)) {
            Db::commit();
                $this->success('修改成功');
        }
        Db::rollback();
        $this->error('修改失败');
    }

    /**
     * 修改产品热门
     * @method(PUT)
     */
    public function hot()
    {
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        Db::startTrans();
        $res[] = $this->service->update($id, ['is_hot' => $this->request->param('is_hot')]);
        $res[] = app(ProductCacheService::class)->deleteAllCache($id);
        if (!empty($res) && ArrayHelper::checkArr($res)) {
            Db::commit();
            $this->success('修改成功');
        }
        Db::rollback();
        $this->error('修改失败');
    }
    /**
     * 产品数据
     * @noAuth(true)
     * @method(GET)
     */
    public function getSelectLabelValueData()
    {
        $data = $this->service->dao->model->select()->toArray();
        $list = [];
        foreach ($data as $key => $value) {
            $list[$key]['label'] = $value['name'];
            $list[$key]['value'] = $value['id'];
        }
        $this->success('获取成功',$list);
    }
    /**
     * 产品数据
     * @noAuth(true)
     * @method(GET)
     */
    public function select()
    {
        $data = $this->service->dao->model->select()->toArray();
        $this->success('获取成功',$data);
    }
}