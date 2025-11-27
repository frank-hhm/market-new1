<?php
/**
 * @Date: 2025/6/30 16:32
 */
declare(strict_types=1);
namespace app\sys\controller\finance;

use app\sys\controller\Base;
use think\facade\{
    App,
    Config
};
use app\common\services\member\MemberCoinService;
/**
 * 钱包
 * Class Coin
 * @package app\sys\controller\finance
 */
class Coin extends Base
{
    /**
     * Water constructor.
     */
    public function __construct(App $app, MemberCoinService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    /**
     * 钱包列表
     * @method(GET)
     */
    public function list()
    {
        $params = $this->request->getMore([
            ['username_like',''],
            ['moni','all'],
            ['agent_id',[]],
            ['table_sorter','']
        ]);
//        dump();die;
        $agentIds = $this->getAgentChildIds();
        $data = $this->service->getList($params,$agentIds);
        $data['balance'] = $this->service->getSum($params,$agentIds);
        $data['yingkui'] = $this->service->getSum($params,$agentIds,'yingkui');
        $data['keyong'] = $this->service->getSum($params,$agentIds,'keyong');
        $data['jingzhi'] = $this->service->getSum($params,$agentIds,'jingzhi');
        $data['count'] =  $data['balance'] + $data['yingkui'];

        $this->success('获取成功',$data);
    }

    /**
     * 钱包详细
     * @noAuth(true)
     * @method(GET)
     */
    public function detail(){
        $this->success($this->service->getDetail($this->request->get('id')));
    }

    /**
     * 余额充值
     * @method(PUT)
     */
    public function rechargeBalance(){
        if (!$id = $this->request->param('id')) {
            $this->error('参数错误!');
        }
        $data = $this->request->postMore([
            'mode',
            'money',
            'remark',
            'source'
        ]);
        if ($this->service->rechargeToBalance($data,$id,$this->adminId,$this->adminInfo['account'])) {
            $this->success('操作成功');
        }
        $this->error($this->service->getError() ?: '操作失败');
    }
}
