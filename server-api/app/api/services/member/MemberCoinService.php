<?php
/**
 * @Date: 2025/6/26 1:22
 */
declare(strict_types=1);
namespace app\api\services\member;

use app\common\services\member\MemberCoinService as CommonMemberCoinService;

/**
 *
 * Class MemberCoinService
 */
class MemberCoinService extends CommonMemberCoinService
{


    public function getMemberCoinSort($data = [],$limit = 10){
        $list = $this->dao->model->with(['member'])->hasWhere('member' , ['moni'=>1])->order(['balance DESC'])->limit($limit)->select()->toArray();
        return $list;
    }

}