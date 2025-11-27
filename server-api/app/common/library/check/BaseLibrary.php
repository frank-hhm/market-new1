<?php
/**
 * @Date: 2025/7/6 20:57
 */
declare(strict_types=1);

namespace app\common\library\check;

class BaseLibrary
{


    // 错误信息
    public mixed $error = '';
    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError(): mixed
    {
        return $this->error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }

    /**
     * 设置错误信息
     * @param $error
     */
    public function setError($error): void
    {
        empty($this->error) && $this->error = $error;
    }

}