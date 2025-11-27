<?php
/**
 * @Date: 2025/2/27 13:50
 */
declare(strict_types=1);
namespace app\common\services\activity;

use app\common\dao\activity\TurntableRecordLogDao;
use app\common\helper\StringHelper;
use app\common\services\BaseService;

/**
 * Class TurntableRecordLogService
 */
class TurntableRecordLogService extends BaseService
{

    /**
     * TurntableRecordLogService constructor.
     * @param TurntableRecordLogDao $dao
     */
    public function __construct(TurntableRecordLogDao $dao)
    {
        $this->dao = $dao;
    }

    public function getListApi(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        if(!empty($param['member_id'])){
            $filter[] = ['member_id', '=',$param['member_id'] ];
        }
        $list = $this->dao->model->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        $list = $this->dao->model->with(['member','agent'])
            ->whereIn("member_id",function ($query) use ($param,$agentIds){
                $map = [];
                if (!empty($param['username_like'])){
                    $map[] = ['real_name|username|mobile', 'like', "%{$param['username_like']}%"];
                }
                if (!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
                    $map[] = ['agent_id', 'in', $param['agent_id']];
                }else{

                    $map[] = ['agent_id', 'in', $agentIds];
                }
                return $query->name("member")->where($map)->field('id');
            })
            ->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
    public function getSum(array $param = [],$agentIds = []): float
    {
        $filter = [];
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        return $this->dao->model->whereIn("member_id",function ($query) use ($param,$agentIds){
            $map = [];
            if (!empty($param['username_like'])){
                $map[] = ['real_name|username|mobile', 'like', "%{$param['username_like']}%"];
            }
            if (!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
                $map[] = ['agent_id', 'in', $param['agent_id']];
            }else{

                $map[] = ['agent_id', 'in', $agentIds];
            }
            return $query->name("member")->where($map)->field('id');
        })
        ->where($filter)->sum('number');
    }
}