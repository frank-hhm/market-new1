<?php
/**
 * @Date: 2023/12/16 13:21
 */

declare(strict_types=1);

namespace app\common\helper;
/**
 * 字符串工具类
 * Class String
 * @package app\common\helper
 */
class StringHelper
{
    /**
     * 转换为驼峰式命名
     */
    public static function convertUnderline($str , $ucfirst = true)
    {
        while(($pos = strpos($str , '_'))!==false)
            $str = substr($str , 0 , $pos).ucfirst(substr($str , $pos+1));
        return $ucfirst ? ucfirst($str) : $str;
    }

    /**
     * 截取两个字符串直接的字符串
     */
    public static function getStringBetween($string, $start, $end): string
    {
        return substr($string, strlen($start) + strpos($string, $start), (strlen($string) - strpos($string, $end)) * (-1));
    }

    /**
     * 驼峰转下划线规则
     */
    public static function nameTolower($name): string
    {
        $dots = [];
        foreach (explode('.', strtr($name, '/', '.')) as $dot) {
            $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
        }
        return strtolower(join('.', $dots));
    }

    public static function isTrue($val, $return_null = false){
        $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return ( $boolval===null && !$return_null ? false : $boolval );
    }

    /**
     * 唯一数字编码
     */
    public static function uniqidNumber($size = 12, $prefix = ''): string
    {
        $time = time() . '';
        if ($size < 10) $size = 10;
        $string = $prefix . (intval($time[0]) + intval($time[1])) . substr($time, 2) . rand(0, 9);
        while (strlen($string) < $size) $string .= rand(0, 9);
        return $string;
    }

    /**
     * 获取随机字符串
     */
    public static function randString($length = 6, $type = 'number', $convert = 0): string
    {
        $config = array(
            'number' => '1234567890',
            'letter' => '#abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all' => '#abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
            'letter1' => 'abcdefghjkmnpqrstuvwxyz',
            'letter2' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        );

        if (!isset($config[$type]))
            $type = 'string';
        $string = $config[$type];

        $code = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[mt_rand(0, $strlen)];
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
    /**
     * 唯一日期编码
     */
    public static function uniqidDate($size = 16, $prefix = ''): string
    {
        if ($size < 14) $size = 14;
        $string = $prefix . date('Ymd') . (date('H') . date('i')) . date('s');
        while (strlen($string) < $size) $string .= rand(0, 9);
        return $string;
    }

    /**
     * 保留两位
     */
    public static function isNumber2($number, $isMinimum = false, $minimum = 0.01)
    {
        if(empty($number) || !is_numeric($number)) return $number;
        $isMinimum && $number = max($minimum, $number);
        return sprintf('%.2f', $number);
    }
    public static function number2($number, $isMinimum = false, $minimum = 0.01): string
    {
        $isMinimum && $number = max($minimum, $number);
        return sprintf('%.2f', $number);
    }
    public static function numberN($number,$precision = 2, $isMinimum = false, $minimum = 0.00): string
    {
        $isMinimum && $number = max($minimum, $number);
        return sprintf("%.{$precision}f", $number);
    }
    public static function bcsub($leftOperand, $rightOperand, $scale = 2): string
    {
        return \bcsub($leftOperand, $rightOperand, $scale);
    }

    public static function bcadd($leftOperand, $rightOperand, $scale = 2): string
    {
        return \bcadd( (string)$leftOperand,  (string)$rightOperand, $scale);
    }

    public static function bcmul($leftOperand, $rightOperand, $scale = 2): string
    {
        return \bcmul((string)$leftOperand, (string)$rightOperand, $scale);
    }

    public static function bcdiv($leftOperand, $rightOperand, $scale = 2): string
    {
        return \bcdiv((string)$leftOperand, (string)$rightOperand, $scale);
    }

    public static function bccomp($leftOperand, $rightOperand, $scale = 2): int
    {
        return \bccomp($leftOperand, $rightOperand, $scale);
    }

    public static function bcequal($leftOperand, $rightOperand, $scale = 2): bool
    {
        return self::bccomp($leftOperand, $rightOperand, $scale) === 0;
    }

    /**
     * 格式化时间
     * @param $time
     * @return string
     */
    public static function formatTime($time) {
        $currentTime = time();
        if(is_integer($time)){
            $timestamp = $time;
        }else{
            $timestamp = strtotime($time);
        }
        $seconds = $currentTime - $timestamp;

        if ($seconds < 60) {
            return $seconds . "秒前";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . "分钟前";
        } elseif ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return $hours . "小时前";
        } elseif ($seconds < 2592000) {
            $days = floor($seconds / 86400);
            return $days . "天前";
        } elseif ($seconds < 31536000) {
            $months = floor($seconds / 2592000);
            return $months . "个月前";
        } else {
            $years = floor($seconds / 31536000);
            return $years . "年前";
        }
    }

    public static function getParamValue($string, $startTag, $endTag) {
        $pattern = '/' . preg_quote($startTag, '/') . '(.*?)' . preg_quote($endTag, '/') . '/';
        preg_match($pattern, $string, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return '';  // 如果未找到匹配，则返回空字符串或自定义的默认值
        }
    }

    public static function  _strtotime($datetime): float|bool|int|string
    {
        if (!is_numeric($datetime)) {
            return strtotime($datetime);
        }else{
            return $datetime;
        }
    }


    /**
     * 日期格式标准输出
     * @param string|int $datetime 输入日期
     * @param string $format 输出格式
     * @return string
     */
    public static function formatDatetime(string|int $datetime, string $format = 'Y-m-d H:i:s'): string
    {
        if (empty($datetime) || $datetime == 0) return '--';
        if (is_numeric($datetime)) {
            return date($format, $datetime);
        } else {
            return date($format, strtotime($datetime));
        }
    }
    public static function calculateDrawChances1($depositAmount): int
    {
        // 初始抽奖机会
        $chances = 0;
        if ($depositAmount >= 1000) {
            // 计算超过1000的部分可以分成多少个1000，并给予相应的抽奖机会
            $chances = floor($depositAmount / 1000) * 1;
        }
        return (int)$chances;
    }


    /**
     * 掩码
     */
    public static function maskName($name) {
        $len = mb_strlen($name, 'UTF-8');

        // 根据长度决定如何处理
        if ($len == 1) {
            return $name;
        } elseif ($len == 2) {
            // 对于两个字符长度的名字，只显示第一个字符
            return mb_substr($name, 0, 1, 'UTF-8');
        } else {
            // 对于长度大于2的字符串，保留首尾字符，中间用**替换
            return mb_substr($name, 0, 1, 'UTF-8') . '**' . mb_substr($name, -1, 1, 'UTF-8');
        }
    }

    public static function createOrderNo(): string
    {
        return date('Ymd') . substr(implode('', array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    public static function isValidDecimal($value): bool
    {
        // 先判断是否为合法数字（包括整数和小数）
        if (!is_numeric($value)) {
            return false;
        }
        // 转为字符串，防止科学计数法
        $value = (string)$value;
        // 使用正则匹配：整数或最多2位小数
        return (bool)preg_match('/^-?\d+(\.\d{1,2})?$/', $value);
    }

    public static  function registrationDuration($create_time,$isText = false): float | string | int
    {
        // 如果是字符串，转换为时间戳
        if (!is_numeric($create_time)) {
            $create_time = strtotime($create_time);
        }

        // 当前时间戳
        $now = time();

        // 时间差（秒）
        $diff = $now - $create_time;

        // 转换为可读格式
        $years = floor($diff / (365 * 24 * 60 * 60));
        $months = floor(($diff % (365 * 24 * 60 * 60)) / (30 * 24 * 60 * 60));
        $days = floor(($diff % (30 * 24 * 60 * 60)) / (24 * 60 * 60));
        $hours = floor(($diff % (24 * 60 * 60)) / (60 * 60));
        $minutes = floor(($diff % (60 * 60)) / 60);
        $seconds = $diff % 60;
        if ($isText){
            // 构建返回字符串（只显示非零部分）
            $parts = [];
            if ($years > 0) $parts[] = $years . '年';
            if ($months > 0) $parts[] = $months . '个月';
            if ($days > 0) $parts[] = $days . '天';
            if ($hours > 0) $parts[] = $hours . '小时';
//            if ($minutes > 0) $parts[] = $minutes . '分钟';
//            if (empty($parts)) $parts[] = $seconds . '秒';
            return implode('', $parts);
        }

        return (int)floor(($diff) / (24 * 60 * 60));;
    }

}