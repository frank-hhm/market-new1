<?php
/**
 * @Date: 2025/6/26 1:50
 */
declare(strict_types=1);
namespace app\common\dao\agent;

use app\common\model\agent\AgentModel;

/**
 * Class AgentDao
 * @package app\common\dao
 */
class AgentDao extends \app\common\dao\BaseDao
{
    protected array $field = ['*'];

    protected function setModel(): string
    {
        return AgentModel::class;
    }


    public function getDetailByAccount($account){
        return $this->model->where(['account' => $account, 'status' => 1])->find();
    }

    /**
     * 当前账号是否可用
     */
    public function isAccountUsable($account, $id)
    {
        return $this->model->where(['account' => $account])->where('id', '<>', $id)->count();
    }


    public function accountById(int $id)
    {
        return $this->model->where(['id' => $id])->field($this->field)->find($id);
    }
}

