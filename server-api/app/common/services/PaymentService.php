<?php
/**
 * @Date: 2025/6/27 17:50
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\dao\PaymentDao;
use app\common\exception\CommonException;
use think\facade\Filesystem;
/**
 * 收款渠道
 * Class PaymentService
 */
class PaymentService extends BaseService
{

    /**
     * PaymentService constructor.
     * @param PaymentDao $dao
     */
    public function __construct(PaymentDao $dao)
    {
        $this->dao = $dao;
    }


    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('渠道不存在');
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
        $list = $this->dao->model->with(['agent'])->where($filter)->order(['sort DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    public function getSelect($agentId = 0){

        $filter = [];
        $filter[] = ['status','=',1];
        $isAgent = false;
        if($agentId > 0 &&  $this->dao->model->where('agent_id',$agentId)->where($filter)->count() > 0){
            $isAgent = true;
        }
        $list = $this->dao->model->with(['agent'])->where($filter)->where(function ($query) use ($isAgent,$agentId){
            $query->when($isAgent,function ($qu) use ($agentId){
                $qu->where('agent_id',$agentId);
//                ->whereOr(['agent_id'=>0]);
            })->when(!$isAgent,function ($qu){
                $qu->where('agent_id',0);
            });
        })->order(['sort DESC'])->select()->toArray();
//        dump($list);die;
        return $list;
    }
}