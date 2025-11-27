<?php
/**
 * @Date: 2025/6/29 19:12
 */
declare(strict_types=1);
namespace app\api\controller\activity;
use think\facade\App;
use app\common\services\activity\TurntableRecordLogService;

/**
 * Class TurntableRecord
 */
class TurntableRecord extends \app\api\controller\Base
{
    /**
     * TurntableRecord constructor.
     * @param App $app
     * @param TurntableRecordLogService $service
     */
    public function __construct(App $app, TurntableRecordLogService $service)
    {
        parent::__construct($app);
        $this->service = $service;
    }

    public function getList(){
        $data = $this->request->getMore([
        ]);
        $data['member_id'] = $this->uid;
        $this->success('获取成功',$this->service->getListApi($data));
    }

    public function test(){
        $this->success('获取成功');
    }
}