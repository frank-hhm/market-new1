<?php
/**
 * @Date: 2025/6/28 10:57
 */
declare(strict_types=1);

namespace app\common\services;

use app\common\dao\BannerDao;
use app\common\exception\CommonException;
use think\facade\Filesystem;
/**
 * 轮播图
 * Class BannerService
 */
class BannerService extends BaseService
{

    /**
     * BannerService constructor.
     * @param BannerDao $dao
     */
    public function __construct(BannerDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('轮播不存在');
        }
        return $detail->toArray();
    }

    public function getList($params = [])
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['time'][0])&&!empty($params['time'][1])){
            $filter[] = ['create_time','>=',strtotime($params['time'][0])];
            $filter[] = ['create_time','<=',strtotime($params['time'][1])+86400];
        }
        $list = $this->dao->model->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    public function getSelect(){
        $list = $this->dao->model->where([
            'status'=>1
        ])->order(['create_time DESC'])->select()->toArray();
        return $list;
    }
}