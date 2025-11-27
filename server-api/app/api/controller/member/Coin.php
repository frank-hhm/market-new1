<?php
/**
 * @Date: 2025/6/29 21:02
 */
declare(strict_types=1);
namespace app\api\controller\member;
use think\facade\App;
use app\api\services\member\MemberCoinService;

/**
 * Class Coin
 */
class Coin extends \app\api\controller\Base
{
    /**
     * MemberService constructor.
     * @param App $app
     * @param MemberCoinService $service
     */
    public function __construct(App $app, MemberCoinService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }


    /**
     * 账户互转
     * @method(POST)
     */
    public function mutualism(){
        $data = $this->request->postMore([
            ['money', ''],
            ['type', ''],
        ]);
        if(empty($data['money']) || empty($data['type'])){
            $this->error('参数错误!');
        }
        if($this->service->mutualism($data,$this->member,$this->uid)){
            $this->success("互转成功");
        }
        $this->error($this->service->getError()?:'互转失败!');
    }

    /**
     * 获取用户资金排序
     * @method(GET)
     *
     */
    public function getMemberCoinSort(){
        $data = $this->request->postMore([
        ]);

        if($data = $this->service->getMemberCoinSort($data)){
            $tipsMsg = sysconf('moni_activity_time_tips');
            empty($tipsMsg) && $tipsMsg ='活动时间：2025年1月20日-2025年3月20日';
            $this->success('成功!',[
                'list'=>$data,
                'config'=>[
                    'time'=>$tipsMsg,
                    'numbers'=>[8888,6666,3333]
                ]
            ]);
        }
        $this->error($this->service->getError()?:'失败!');
    }
}