<?php
/**
 * @Date: 2025/7/6 17:27
 */
declare(strict_types=1);
namespace app\common\services;


use app\common\dao\OperateLogsDao;
use app\common\enum\logs\OperateSourceEnum;
use app\common\helper\ArrayHelper;
use app\common\helper\StringHelper;
use app\common\services\BaseService;
use think\facade\Db;
use think\facade\Log;

/**
 * 日记
 * Class OperateLogsService
 */
class OperateLogsService extends BaseService
{

    /**
     * OperateLogsService constructor.
     * @param OperateLogsDao $dao
     */
    public function __construct(OperateLogsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList($params = []){
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        $source = $params['source'] ?? OperateSourceEnum::ADMIN;
        if(!empty($params['account_like'])){
        }
        $tableName = $this->dao->model->getTable();
        if(!empty($params['time']) && is_array($params['time'])){
            $filter[] = [$tableName.'.create_time', 'between', [StringHelper::_strtotime($params['time'][0]), StringHelper::_strtotime($params['time'][1])]];
        }
//        dump($filter);die;
        return $this->dao->model->with([$source])
            ->when($source === OperateSourceEnum::MEMBER,function ($query) use ($params,$source){
                $query->hasWhere($source,function ($q) use ($params,$source) {
                    $q->where('username|nickname|mobile',"like","%".$params['account_like']."%");
                });
            })
            ->when($source === OperateSourceEnum::ADMIN || $source === OperateSourceEnum::AGENT,function ($query) use ($params,$source){
                $query->hasWhere($source,function ($q) use ($params,$source) {
                    $q->where('account|real_name',"like","%".$params['account_like']."%");
                });
            })
            ->where('source', '=', $source)->where($filter)->order(['id DESC'])->page($page)->paginate($limit)->toArray();
    }

    public function getLogData($param = []): array
    {
        $data['ip'] = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ??  request()->ip();
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        is_array($param) && $param = json_encode($param,JSON_UNESCAPED_UNICODE);
        $data['param_data'] = $param;
        $data['method'] = request()->method();
        return $data;
    }


    public function createLog($sourceId,$source,$type = '',$message = '',$content = '')
    {
        $data['source_id'] = $sourceId;
        $data['source'] = $source;
        $data['message'] = $message;
        $data['content'] = $content;
        $data['type'] = $type;
        $data['create_time'] = time();

        $logs = array_merge($data,$this->getLogData(request()->param()));
        Db::startTrans();
        $res[] = $this->dao->model->create($logs);
        if(ArrayHelper::checkArr($res)){
            Db::commit();
            return $res;
        }else{
            Db::rollback();
            Log::write($logs,'error');
            return false;
        }
    }


}