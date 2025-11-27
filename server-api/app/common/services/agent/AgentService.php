<?php
/**
 * @Date: 2025/6/26 1:23
 */
declare(strict_types=1);

namespace app\common\services\agent;

use app\common\dao\agent\AgentDao;
use app\common\exception\CommonException;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\CacheService;
use app\common\services\ProductService;
use think\facade\Db;

/**
 * 代理商
 * Class AgentService
 */
class AgentService extends BaseService
{
    /**
     * AgentService constructor.
     * @param AgentDao $dao
     */
    public function __construct(AgentDao $dao)
    {
        $this->dao = $dao;
    }


    public function accountByToId(string $account)
    {
        return $this->dao->model->where(['account' => $account, 'status' => 1])->value('id');
    }

    public function getDetail($filter,$exception = true)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail ) {
            if(!$exception){
                return [];
            }
            throw new CommonException('代理商不存在');
        }
        return $detail->toArray();
    }


    public function getAgentCache($id)
    {
        $cacheKey = 'agent:detail:'.$id;
        $cacheService = app(CacheService::class);
        $cache = $cacheService->get($cacheKey);
        if(empty($cache)){
            $cache = $this->dao->model->where(['id' => $id])->find();
            if(!empty($cache)){
                $cache = $cache->toArray();
            }
            $cacheService->set($cacheKey,$cache,60*30);
        }
        return $cache;
    }

    public function getAgentByInviteCode($code){
        $detail = $this->dao->model->where(['invite_code' => $code])->find();
        return $detail['id'] ?? 0;
    }

    public function getInviteCode()
    {
        $code = 'A'.StringHelper::randString(7, 'string');
        if ($this->dao->model->where([
                'invite_code' => $code
            ])->limit(1)->count() > 0) {
            $code = $this->getInviteCode();
        }
        return $code;
    }

    public function getLevelAgentSelect($pidLevel = 1){
        $filter = [];
        $filter[] = ['delete_time','=',0];
        $filter[] = ['level','=',$pidLevel - 1];
        return $this->dao->model->where($filter)->field(['id','real_name','account'])->select()->toArray();
    }

    public function getLevelAgentSelect2($pidLevel = 1,$pids = []){
        $filter = [];
        $filter[] = ['delete_time','=',0];
        $filter[] = ['level','=',$pidLevel];
        if ($pids && count($pids) > 0){
            $filter[] = ['pid','in',$pids];
        }
        return $this->dao->model->where($filter)->field(['id','real_name','account'])->select()->toArray();
    }
    public function getAgentSelectByPid($pid = 0){
        $filter = [];
        $filter[] = ['delete_time','=',0];
        if ($pid !== 0){
            if($this->dao->model->where('id',$pid)->count() === 0) {
                return [];
            }
            $filter[] = ['pid','=',$pid];
        }
        return $this->dao->model->where($filter)->field(['id','real_name','account'])->select()->toArray();
    }

    public function getAgentSelectByIds($ids = []){
        $filter = [];
        $filter[] = ['delete_time','=',0];
        return $this->dao->model->whereIn('id',$ids)->where($filter)->field(['id','real_name','account'])->select()->toArray();
    }


    /**
     * 获取列表
     */
    public function getAgentList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $filterMap = [
            [
                ['pid', 'in', $agentIds]
            ],
            [
                ['id', 'in', $agentIds]
            ]
        ];

        if(!empty($param['account_like'])){
            $filter[] = ['account','like',"%{$param['account_like']}%"];
        }
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        $list = $this->dao->model->where($filter)->when($filterMap,function($query) use ($filterMap){
            $query->where(function ($query1) use ($filterMap) {
                $query1->whereOr($filterMap);
            });
        })->page($page)->paginate($limit)->toArray();
        return $list;
    }


    /**
     * 修改账号
     */
    public function updateSave($id, array $data): bool
    {
        if (!$agentInfo = $this->getDetail($id)) {
            throw new CommonException('代理商不存在,无法修改');
        }
        if ($agentInfo['delete_time']) {
            throw new CommonException('代理商已经删除');
        }
        //修改密码
        if ($data['pwd']) {
            $data['pwd'] = $this->dao->passwordHash($data['pwd']);
        }else{
            unset($data['pwd']);
        }
        //修改账号
        if (isset($data['account']) && $data['account'] != $agentInfo['account'] && $this->dao->isAccountUsable($data['account'], (int)$id)) {
            throw new CommonException('账号已存在');
        }

        Db::startTrans();
        try {

            $this->dao->update($id,$data);
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }



    public function getInitFee($fee){
        if(empty($fee)){
            $proSelect = app(ProductService::class)->dao->model->order('sort desc,id asc')->column('id,name','id');
            $feeList = [];
            foreach ($proSelect as $item){
                $feeList[$item['id']] = [
                    'dist'=>0,
                    'markup'=>0
                ];
            }
            return $feeList;
        }else{
            return json_decode($fee,true);
        }
    }



    /**
     * 获取级联
     */
    public function getCascaderData(): array
    {
        $list = $this->dao->model->where("delete_time" , 0)->field( ['id as value', 'pid', 'real_name as label'])->order('id DESC')->select()->toArray();
        if (!empty($value)) {
            $data = ArrayHelper::getArrayTreeValue($list, $value);
            foreach ($list as $key => &$item) {
                if(in_array($item['value'] , $data) || $item['pid'] == $value) $item['disabled'] = true;
            }
        } else {
            $data = [];
        }
        return [ArrayHelper::getArrayTreeChildren($list, 'children', 'value'), array_reverse($data)];
    }
}