<?php
/**
 * @Date: 2025/7/13 23:28
 */
declare(strict_types=1);
namespace app\api\controller;

use app\common\services\NoticeService;

/**
 * Class Notice
 */
class Notice extends \app\api\controller\Base
{
    /**
     *
     * @force(false)
     * @method(GET)
     */
    public function list()
    {
        $data = app(NoticeService::class)->getList($this->request->param());
        $this->success('获取成功',$data);
    }

    /**
     * 公告详细
     * @force(false)
     * @method(GET)
     */
    public function detail(){
        $this->success('获取成功',app(NoticeService::class)->getDetail($this->request->get('id')));
    }


}