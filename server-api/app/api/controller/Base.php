<?php
/**
 * @Date: 2025/1/14 17:52
 */
declare(strict_types=1);
namespace app\api\controller;
use app\BaseController;
use think\App;
/**
 * 控制器基础类
 */
class Base extends BaseController
{

    /**
     * 当前登陆用户信息
     */
    protected mixed $member = null;

    /**
     * 当前登陆用户ID
     */
    protected int $uid = 0;

    // 初始化
    protected function initialize()
    {
        if($this->request->isLogin()){
            $this->uid = $this->request->uid() ?? 0;
            $this->member = $this->request->member() ?? [];
        }
    }

    public function checkBind()
    {
        if(!empty($this->member['bind_status']['value']) && $this->member['bind_status']['value'] == 1){
            return true;
        }
        if(!empty($this->member['bind_status']['value']) && $this->member['bind_status']['value'] == 2){
            $this->error('实名认证正在审核中...请稍后再试！');
        }
        $this->error('请先实名认证');
    }
}