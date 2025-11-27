<?php
/**
 * @Date: 2025/7/7 14:43
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\dao\VersionDataDao;

/**
 * 版本管理
 * Class VersionDataService
 */
class VersionDataService extends BaseService
{
    /**
     * VersionDataService constructor.
     * @param VersionDataDao $dao
     */
    public function __construct(VersionDataDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList($params = []): array
    {
        [$page, $limit] = $this->dao->getPageValue();
        return $this->dao->model->order('create_time DESC')->page($page)->paginate($limit)->toArray();
    }
}