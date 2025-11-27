<?php
/**

 * @Date: 2024/2/23 10:11
 */
declare(strict_types=1);
namespace app\common\services\finance;

use app\common\dao\finance\WaterDao;

use app\common\enum\EnumFactory;
use app\common\enum\finance\RechargePayTypeEnum;
use app\common\enum\finance\SourceEnum;
use app\common\enum\finance\WithdrawalTypeEnum;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use think\facade\Db;

/**
 * 流水
 * Class WaterService
 */
class WaterService extends BaseService
{
    /**
     * WaterService constructor.
     * @param WaterDao $dao
     */
    public function __construct(WaterDao $dao)
    {
        $this->dao = $dao;
    }


    /**
     * 新增
     */
    public function createSave($source, $data, $describeParam)
    {
        //vsprintf(EnumFactory::instance('finance.source')->getItem($source)['describe'], $describeParam)
        return $this->dao->model->create(array_merge([
            'source' => $source,
            'describe' => EnumFactory::instance('finance.source')->getItem($source)['describe'],
        ], $data));
    }


    public function getList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = $this->getFilter($param,$agentIds,'member');
        $list = $this->dao->model->with(['member','agent'])->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    /*
     * 获取代理商流水
     */

    public function getAgentList(array $param = [],$agentIds = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = $this->getFilter($param,$agentIds,'agent');
        $list = $this->dao->model->with(['member','agent'])->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }

    public function getSum(array $param = [],$agentIds = [],$key = 'money',$type = 'member')
    {
        $filter = $this->getFilter($param,$agentIds,$type);
        $sourceEnum = SourceEnum::data();
        $list = [];
        foreach ($sourceEnum as $k => $item){
            if(($type === 'member' && !in_array( $item['value'] , [
                SourceEnum::FEE,
                SourceEnum::AGENT_WITHDRAWAL
                    ])) || ($type === 'agent' && in_array( $item['value'] , [
                        SourceEnum::FEE,
                        SourceEnum::AGENT_WITHDRAWAL
                    ]))){
                $list[] = [
                    'name' => $item['name'],
                    'value' => $this->dao->model->where('source', '=', $k)->where($filter)->sum($key)
                ];
            }
        }
        return $list;
    }



    public function getFilter(array $param = [],$agentIds = [],$type = 'member')
    {
        $filter = [];
        if(!empty($param['time']) && is_array($param['time'])){
            $filter[] = ['create_time', 'between', [StringHelper::_strtotime($param['time'][0]), StringHelper::_strtotime($param['time'][1])]];
        }
        if(!empty($param['desc']) ){
            $filter[] = ['describe', 'like', "%{$param['desc']}%"];
        }
        if(!empty($param['pay_type']) ){
            $filter[] = ['pay_type', '=', $param['pay_type']];
        }
        if(!empty($param['source']) ){
            $filter[] = ['source', '=', $param['source']];
        }
        if(!empty($param['username_like']) ){
            $filter[] =  ['member_id', 'in', Db::name('member')->where([['username|mobile|nickname', 'like', "%{$param['username_like']}%"]])->column('id')];
        }
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
            $filter[] = ['agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = ['agent_id','in',$agentIds];
        }
        if(!empty($param['create_time'][0]) && !empty($param['create_time'][1])){
            $filter[] = ['create_time','>=',StringHelper::_strtotime($param['create_time'][0])];
            $filter[] = ['create_time','<=',StringHelper::_strtotime($param['create_time'][1])];
        }
        if($type === 'member'){
            $filter[] = ['source', '<>', 'fee'];
        }elseif ($type === 'agent'){
            $filter[] = ['source', '=', 'fee'];
        }
        return $filter;
    }


    public function getListApi(array $param = [],$source = [],$payType = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($param['start_date'])){
            $filter[] = ['create_time', '>=', StringHelper::_strtotime($param['start_date'] . " 00:00:00"),];
        }
        if(!empty($param['end_date'])){
            $filter[] = ['create_time', '<=', StringHelper::_strtotime($param['end_date'] . " 23:59:59"),];
        }
        if(!empty($param['member_id'])){
//            $filter[] = ['member_id', '=',$param['member_id'] ];
        }
        $list = $this->dao->model->where($filter)
            ->where("source",'in',$source)
            ->when(in_array(SourceEnum::MEMBER_COMMISSION_WITHDRAWAL,$source),function ($query) use ($source){
                $query->whereOr([
                    [
                        ["source","=",SourceEnum::MEMBER_COMMISSION_WITHDRAWAL],
                        ["pay_type","=",RechargePayTypeEnum::COMMISSION_BALANCE],

                    ],
                    [
                        ["source","<>",SourceEnum::MEMBER_COMMISSION_WITHDRAWAL],
                    ]
                ]);
            })
            ->order(['create_time DESC'])
            ->page($page)->paginate($limit)->toArray();
        foreach ($source as $sourceItem){
            $list[$sourceItem] =  $this->dao->model->where($filter)
                ->where([
                    "source"=>$sourceItem
                ])->whereIn("pay_type",$payType)->sum('money');
        }
        return $list;
    }
}