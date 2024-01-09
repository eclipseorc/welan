<?php
namespace WeLan\Lib;

use DateTime;
use Exception;

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
     * 手机号码脱敏
     * @param $mobile
     * @return array|string|string[]
     * User: oldtom
     * Date: 2023/12/6 18:00
     */
    public static function mobilePartialMask($mobile = '')
    {
        if (strlen($mobile) >= 11) {
            return substr_replace($mobile, '****', -4, 4);
        } else {
            return $mobile;
        }
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
     * 获取指定长度的随机字符串
     * @param $length
     * @param $chars
     * @return string
     * author   oldtom
     * date: 2023/10/5 18:16
     */
    public static function getRandomString($length, $chars = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890'): string
    {
        $hash   = '';
        $max    = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 按日期生成指定长度的流水号
     * @param int $length
     * @return string
     * author   oldtom
     * date     2023/3/30 19:02
     */
    public static function getOrderNo($length = 8) {
        $bytes = random_bytes(ceil($length / 2));
        return date('Ymd') . substr(bin2hex($bytes), 0, $length);
    }

    /**
     * 根据输入的filed生成一个包含array中对应key值新的数组
     * @param $field - 英文逗号分割的字符串
     * @param $array - 数组
     * @return array
     * user: oldtom
     * date: 2023/10/5 18:16
     */
    public static function arrayCapture($field = '', $array = [])
    {
        $data = [];
        if (!empty($field)) {
            $explodeArr = explode(',', $field);
            if (!empty($explodeArr) && is_array($explodeArr)) {
                foreach ($explodeArr as $key => $value) {
                    if (isset($array[$value])) {
                        $data[$value] = $array[$value];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 时间转文本
     * @param $datetime
     * @return string
     * @throws Exception
     * User: zhangliangliang
     * Date: 2023/12/7 15:31
     */
    public static function timeToText($datetime = '')
    {
        $nowObj = new \DateTime();
        try {
            $paramObj = new \DateTime($datetime);
        } catch (Exception $e) {
            throw new Exception('时间格式错误');
        }
        $interval = $paramObj->diff($nowObj);
        $daysDiff = $interval->d;
        $hoursDiff = $interval->h;
        $minutesDiff = $interval->i;
        if ($daysDiff > 0) {
            return $daysDiff . '天前';
        } elseif ($hoursDiff > 0) {
            return $hoursDiff . '小时前';
        } elseif ($minutesDiff > 0) {
            return $minutesDiff . '分钟前';
        } else {
            return '刚刚';
        }
    }

    /**
     * 网络安全的base64编码
     * @param string $input
     * @return array|string|string[]
     * User: zhangliangliang
     * Date: 2023/12/6 18:27
     */
    public static function base64UrlEncode($input = '')
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * 网络安全的base64解码
     * @param string $input
     * @return false|string
     * User: zhangliangliang
     * Date: 2023/12/6 18:26
     */
    public static function base64UrlDecode($input = '')
    {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * 检测身份证号码，支持大陆，香港台湾澳门
     * @param string $idCardNo
     * @return bool
     * User: zhangliangliang
     * Date: 2024/1/9 16:56
     */
    public static function isIdCardNo($idCardNo = '')
    {
        $preg = '/^(\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX]))'
            . '|(((\s?[A-Za-z])|([A-Za-z]{2}))\d{6}(\([0−9aA]\)|[0-9aA]))'
            . '|([a-zA-Z][0-9]{9})'
            . '|([1|5|7][0-9]{6}\([0-9Aa]\))$/';
        if (preg_match($preg, $idCardNo)) {
            return true;
        }
        return false;
    }

    /**
     * 通过生日计算年龄
     * @param $birthday
     * @return string
     * @throws Exception
     * User: zhangliangliang
     * Date: 2024/1/9 17:02
     */
    public static function calAge($birthday = '')
    {
        $birthday = new DateTime($birthday);
        $now = new DateTime();
        $age = $now->diff($birthday);
        return $age->format('%y');
    }

    /**
     * xml转数组
     * @param $xml
     * @return array
     * User: zhangliangliang
     * Date: 2024/1/9 17:05
     */
    public static function xmlToArray($xml)
    {
        return (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * 数组转xml
     * @param $array
     * @return string
     * User: zhangliangliang
     * Date: 2024/1/9 17:06
     */
    public static function arrayToXml($array)
    {
        if (!is_array($array) || count($array) <= 0) {
            return strval($array);
        }
        $xml = "<xml>";
        foreach ($array as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
}
