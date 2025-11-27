<?php
/**
 * @Date: 2025/7/1 15:58
 */
declare(strict_types=1);
namespace app\api\controller;

use app\common\services\ArticleService;

/**
 * Class Publics
 */
class Article extends \app\api\controller\Base
{
    /**
     *
     * @force(false)
     * @method(GET)
     */
    public function list()
    {
        $data = app(ArticleService::class)->getList($this->request->param());
        $this->success('获取成功',$data);
    }

    /**
     * 文章详细
     * @force(false)
     * @method(GET)
     */
    public function detail(){
        $this->success('获取成功',app(ArticleService::class)->getDetail($this->request->get('id')));
    }
}