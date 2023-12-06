<?php
namespace WeLan\Lib;

/**
 * 页面执行时间
 * Class Runtime
 * @package WeLan\Lib
 * author  oldtom
 * date    2023/3/30 17:31
 */
class Runtime
{
    public static $startTime;

    /**
     * 请求开始时间
     * author   oldtom
     * date     2023/3/30 16:31
     */
    public static function start()
    {
        self::$startTime = microtime(true);
    }

    /**
     * @param int $type
     * @return string
     * author   oldtom
     * date     2023/3/30 16:36
     */
    public static function getRuntime($type = 0)
    {
        $startTime  = self::$startTime;
        $runtime    = (microtime(true) - $startTime) * 1000;
        return round($runtime, 2) . 'ms';
    }
}