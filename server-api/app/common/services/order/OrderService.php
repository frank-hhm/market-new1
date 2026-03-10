<?php
/**
 * @Date: 2025/6/27 15:51
 */
declare(strict_types=1);

namespace app\common\services\order;

use app\common\constants\CacheKeyConstant;
use app\common\dao\order\OrderDao;
use app\common\helper\ProductHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use app\common\services\common\CacheService;
use app\common\services\ProductCacheService;

/**
 * 订单
 * Class OrderService
 */
class OrderService extends BaseService
{

    /**
     * OrderService constructor.
     * @param OrderDao $dao
     */
    public function __construct(OrderDao $dao)
    {
        $this->dao = $dao;
    }

    public function getPinList(array $param = [],$agentIds = [],$isMoni = 0){
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $tableName = $this->dao->model->getTable();
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
//            $filter[] = [$tableName.'.agent_id', '=', $param['agent_id']];
            $filter[] = [$tableName.'.agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = [$tableName.'.agent_id','in',$agentIds];
        }
//        $filter[] = [$tableName.'.agent_id','in',$agentIds];
//        $filter[] = [];
        if($param['ostyle'] != ''){
            $filter[] = ['ostyle','=',$param['ostyle']];
        }
        if(!empty($param['buytime'][0]) && !empty($param['buytime'][1])){
            $filter[] = ['buytime','>=',StringHelper::_strtotime($param['buytime'][0])];
            $filter[] = ['buytime','<=',StringHelper::_strtotime($param['buytime'][1])];
        }
        if(!empty($param['selltime'][0]) && !empty($param['selltime'][1])){
            $filter[] = ['selltime','>=',StringHelper::_strtotime($param['selltime'][0])];
            $filter[] = ['selltime','<=',StringHelper::_strtotime($param['selltime'][1])];
        }
        if(isset($param['is_wei']) && $param['is_wei'] !== 'all' && $param['is_wei'] !== ''){
            $filter[] = ['is_wei','=',$param['is_wei']];
        }
        if(isset($param['pid']) && (is_array($param["pid"]) && count($param['pid']) > 0)){
            $filter[] = ['pid','in',$param['pid']];
        }
        $list = $this->dao->model->with(['member','agent'])->hasWhere('member',[
            ['username|mobile','like','%'.$param['username_like'].'%'],
            ['moni','=',$isMoni]
        ]) ->where('id', 'IN', function ($query) use ($agentIds) {
            $query->table('m_order')->whereIn('agent_id',$agentIds)->field('id');
        })->where($filter)->where([
            ['ostaus','<>',0],
            ['ostaus','<>',2],
            ['ostaus','<>',3]
        ])->order(['selltime DESC'])->page($page)->paginate($limit)->toArray();

        $feeCount = $this->getFeeCount($filter,[
            ['ostaus','<>',0],
            ['ostaus','<>',2],
            ['ostaus','<>',3],
        ],$param,$isMoni,'SUM(ploss) as money_profit,SUM(fee) as money_fee,SUM(sx_fee) as money_sx_fee,SUM(onumber) as onumber');
        $yongCount = $this->getYongCount($filter,$agentIds,[
            ['ostaus','=',1],
        ],$param,$isMoni,'SUM(yong_members_money) as yong_members_money,SUM(yong_agents_money) as yong_agents_money');

        return array_merge($feeCount,$yongCount,$list);
    }

    public function getChiList(array $param = [],$agentIds = [],$isMoni = 0){
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $tableName = $this->dao->model->getTable();
//        $filter[] = [$tableName.'.agent_id','in',$agentIds];
        if(!empty($param['agent_id']) && $param['agent_id'] !== 'all' ){
            $filter[] = [$tableName.'.agent_id',  is_array($param['agent_id'])?'in':'=', $param['agent_id']];
        }else{
            $filter[] = [$tableName.'.agent_id','in',$agentIds];
        }
        if(isset($param['ostyle']) && $param['ostyle'] != ''){
            $filter[] = ['ostyle','=',$param['ostyle']];
        }
        if(!empty($param['buytime'][0]) && !empty($param['buytime'][1])){
            $filter[] = ['buytime','>=',StringHelper::_strtotime($param['buytime'][0])];
            $filter[] = ['buytime','<=',StringHelper::_strtotime($param['buytime'][1])];
        }
        $filterCount =  [['ostaus','=',0]];
        if(isset($param['is_wei']) && $param['is_wei'] !== 'all' && $param['is_wei'] !== ''){
            $filter[] = ['is_wei','=',$param['is_wei']];
        }
        if(isset($param['pid']) && (is_array($param["pid"]) && count($param['pid']) > 0)){
            $filter[] = ['pid','in',$param['pid']];
        }
        if(isset($param['ostaus']) ){
            $filter[] = ['ostaus','=',$param['ostaus']];
            $filterCount =  [['ostaus','=',$param['ostaus']]];
        }else{
            $filter[] = ['ostaus','<>',1];
            $filter[] = ['ostaus','<>',3];
        }
        //去除平仓和撤单
        $list = $this->dao->model->with(['member','agent'])->hasWhere('member',[
            ['username|mobile','like','%'.$param['username_like'].'%'],
            ['moni','=',$isMoni]
        ])->where('id', 'IN', function ($query) use ($agentIds) {
            $query->table('m_order')->whereIn('agent_id',$agentIds)->where('agent_id','<>',2)->field('id');
        })->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();
        //计算浮动盈亏

        $marketCacheService = app(CacheService::class)->setRedisName(CacheKeyConstant::PRODUCT_MARKET_REDIS_DRIVER);
        $productCacheService = app(ProductCacheService::class);
        foreach ($list['data'] as $key => &$item) {
            //如果平仓
            if ($item['ostaus']['value'] == 2){
                $item['yinkui'] = 0;
                continue;
            }

            //默认买涨
            $productDetail = $productCacheService->getProductDetailCache($item['pid']);

            $productNotPrice = $marketCacheService->get(CacheKeyConstant::PRODUCT_PRICE.":".$item['pid']);
            //默认买涨
            if(empty($productNotPrice)){
                $productNotPrice = $productDetail['price'];
            }
            //平仓盈亏
            if($productDetail['number'] != 0){
                $item['yinkui'] = ProductHelper::orderProfit($productNotPrice, $productDetail['number'], $productDetail['money'], $item['buyprice'], $item['onumber']);
            }
            //如果买跌
            $item['ostyle']['value'] == 1 && $item['yinkui'] = $item['yinkui'] * -1;
            $item['yinkui'] = round($item['yinkui'],2);
        }
        $feeCount = $this->getFeeCount($filter,[
        ],$param,$isMoni,'SUM(ploss) as money_profit,SUM(fee) as money_fee,SUM(sx_fee) as money_sx_fee,SUM(onumber) as onumber');
//        $yongCount = $this->getYongCount($filter,$agentIds,$filterCount,$param,$isMoni,'SUM(yingkui) as ying_kui');

        $list['ying_kui'] = array_sum(array_column($list['data'], 'yinkui'));
        return array_merge($feeCount,$list);
    }

    public function getFeeCount($filter = [],$ostaus = [],$param = [],$isMoni = 0,$field = "COUNT()"){
        $tableName = $this->dao->model->getTable();
        $data =  $this->dao->model
            ->join('member m', $tableName.'.member_id = m.id', 'LEFT')
            ->field($field)
            ->where($filter)
            //去除持仓和委托和委托
            ->where($ostaus)
            ->where(function ($query) use ($param,$isMoni){
                $query->where([
                    ['m.moni','=',$isMoni],
                    ['m.agent_id','<>',2],
                    ['m.username|m.mobile','like','%'.$param['username_like'].'%']
                ]);
            })->find()->toArray();
        return $data;
    }

    public function getYongCount($filter,$agentIds,$ostaus = [],$param = [],$isMoni = 0,$field = "COUNT()"){
        $tableName = $this->dao->model->getTable();
        $yong = $this->dao->model
            ->field($field)
            ->where($filter)
            //去除持仓和委托
            ->where($ostaus)
            ->where($tableName.'.agent_id','<>',2)
            ->where($tableName.'.member_id', 'IN', function ($query) use ($param,$isMoni,$agentIds) {
                $query->name('member')->whereIn('agent_id',$agentIds)
                    ->where([['moni','=',$isMoni],
                    ['username|mobile','like','%'.$param['username_like'].'%']
                ])->field('id');
            })
            ->find()->toArray();
        return $yong;
    }



}