<?php
namespace WeLan\Lib;

/**
 * 工具类
 * Class Tools
 * @package WeLan\Lib
 * author  oldtom
 * date    2023/3/30 17:31
 */
class Tools
{
    /**
     * 构造树状菜单
     * @param array $list       集合列表
     * @param string $pidKey    父id key
     * @param int $pid          父id value
     * @param string $childKey  子id key
     * @return array
     * author   oldtom
     * date     2023/3/30 17:35
     */
    public static function buildTree($list = [], $pidKey = 'pid', $pid = 0, $childKey = 'child')
    {
        $tree   = [];
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                if ($value[$pidKey] == $pid) {
                    $value[$childKey] = self::buildTree($list, $pidKey, $value['id'], $childKey);
                    $tree[] = $value;
                }
            }
        }
        return $tree;
    }

    /**
     * 获取指定目录下的所有文件夹名称
     * @param $dir
     * @return array
     * author   oldtom
     * date     2023/3/30 17:39
     */
    public static function getFolders($dir)
    {
        $pathList   = glob($dir . '/*');
        $folderList = [];
        if (!empty($pathList) && is_array($pathList)) {
            foreach ($pathList as $key => $value) {
                if (is_dir($value)) {
                    $folderList[] = str_replace($dir . '/', '', $value);
                }
            }
        }
        return $folderList;
    }

    /**
     * 获取客户端ip地址
     * @param int $type 0获取点分ip，1获取整数ip
     * @return mixed
     * author   oldtom
     * date     2023/3/30 18:55
     */
    public static function getClientIp($type = 0) {
        $type    = $type ? 1 : 0;
        static $ip =  NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr  =  explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos  =  array_search('unknown',$arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip   =  trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip   =  $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip   =  $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = ip2long($ip);
        $ip  = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 获取指定长度的随机字符
     * @param $length   - 长度
     * @param string $chars 指定字符集
     * @return string
     * author   oldtom
     * date     2023/3/30 18:59
     */
    public static function getRandomNum($length, $chars = '1294567890') {
        $hash   = '';
        $max    = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 按日期生成指定长度的订单流水号
     * @param int $length
     * @return string
     * author   oldtom
     * date     2023/3/30 19:02
     */
    public static function getOrderNo($length = 8) {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $length);
    }
}
