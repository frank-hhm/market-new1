<?php
/**
 * @Date: 2025/7/7 15:34
 */
declare(strict_types=1);
namespace app\common\library\api;

class PublishVersionService extends BaseApiService
{

    public function send($data): array
    {
        $baseUatUrl = baseEnvUrl();
        $res = $this->post($baseUatUrl."/api/common.publics/publishVersionDefault",[
            'json' => $data
        ]);
        return $res;
    }

}