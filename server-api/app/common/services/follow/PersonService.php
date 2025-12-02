<?php
/**
 * @Date: 2025/6/28 10:57
 */
declare(strict_types=1);

namespace app\common\services\follow;

use app\common\dao\follow\PersonDao;
use app\common\exception\CommonException;
use app\common\services\BaseService;
use think\facade\Filesystem;
use think\facade\Queue;

/**
 * 交易员
 * Class PersonService
 */
class PersonService extends BaseService
{

    /**
     * PersonService constructor.
     * @param PersonDao $dao
     */
    public function __construct(PersonDao $dao)
    {
        $this->dao = $dao;
    }

    public function isExists($filter){
        return $this->dao->model->where($filter)->limit(1)->count();;
    }
    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('交易员不存在');
        }
        return $detail->toArray();
    }

    public function sendSubscribeMessage($memberId = 0,$productName = ""){
        $person = $this->dao->model->where([
            "member_id" => $memberId
        ])->order("id DESC")->find();
        if(empty($person)){
            return false;
        }

        Queue::push("app\common\jobs\CheckMemberSubscribeJob", [
            'source_id' => $this->uid,
            'source' => "follow_person",
            'message' => "您订阅的".$person["nickname"] . "交易员 ".date("Y-m-d H:i:s")."下单".$productName,
        ], 'CheckMemberSubscribeJob');

        return true;
    }
    public function getList($params = [])
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['time'][0])&&!empty($params['time'][1])){
            $filter[] = ['create_time','>=',strtotime($params['time'][0])];
            $filter[] = ['create_time','<=',strtotime($params['time'][1])+86400];
        }
        $list = $this->dao->model->with(["member"])->withCount(['personOrder' => function($query, &$alias) {
            $query->where("status",1);
            $alias = 'member_count';
        }])->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
}