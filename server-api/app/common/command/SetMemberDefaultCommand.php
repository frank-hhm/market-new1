<?php
/**
 * @Date: 2025/7/6 17:07
 */
declare(strict_types=1);
namespace app\common\command;

use app\common\services\common\CacheService;
use app\common\services\ProductCollectService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class SetMemberDefaultCommand extends Command
{
    protected function configure()
    {
        $this->setName('SetMemberDefaultCommand');
    }

    protected function execute(Input $input, Output $output)
    {

        $member = Db::table('m_member')->column('id');
        #新增自选至默认

        $memberDefaultProduct = sysconf('member_default_product|json');
        if(!empty($memberDefaultProduct)){
            $collects = [];
            foreach ($memberDefaultProduct as $productId){
                foreach ($member as $item) {
                    $collects[] = [
                        'member_id'=> $item,
                        'pid'=> $productId,
                        'create_time'=> time(),
                        'update_time'=> time(),
                    ];
                }
            }
            $res[] = app(ProductCollectService::class)->dao->model->insertAll($collects);
        }

        dump("完成",count($res));
        die;
    }
}