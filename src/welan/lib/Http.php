<?php
namespace WeLan\Lib;

/**
 * http请求
 * Class Http
 * @package WeLan\Lib
 * author  oldtom
 * date    2023/3/30 17:30
 */
class Http
{
    /**
     * get请求
     * @param $url - 请求地址
     * @param int $timeOut  超时时间
     * @param int $noBody   1时将不输出 BODY 部分。同时 Mehtod 变成了 HEAD 特殊用法
     * @return bool|string
     * author   oldtom
     * date     2023/3/30 16:57
     */
    public static function HttpGet($url, $timeOut = 5, $noBody = 0)
    {
        $timeOut    = max((int)$timeOut, 0);
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.8) Gecko/20120306 Firefox/5.0.1');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        if ($noBody != 0) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $str        = curl_exec($ch);
        curl_close($ch);
        return $str;
    }

    /**
     * post请求
     * @param $url - 请求地址
     * @param null $data    请求数据（支持json）
     * @param array $header 请求头
     * @param int $timeOut  超时时间
     * @return bool|string
     * author   oldtom
     * date     2023/3/30 17:18
     */
    public static function HttpPost($url, $data = null, $header = [], $timeOut = 5)
    {
        $timeOut    = max((int)$timeOut, 0);
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        //https 请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_array($data) && 0 < count($data)) {
            $postBody = "";
            $postMulti= false;
            foreach ($data as $key => $value) {
                if ("@" != substr($value, 0, 1)) {
                    // 判断是不是文件上传
                    $postBody .= "$key=" . urlencode($value) . "&";
                } else {
                    // 文件上传用multipart/form-data，否则使用www-form-urlencoded
                    $postMulti = true;
                }
            }
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMulti) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBody, 0, -1));
            }
        } elseif (!empty($data)) {
            // 支持json
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $jsonHeader = ['Content-Type: application/json;charset=utf-8', 'Content-Length:' . strlen((string)$data)];
        }
        if (is_array($header) && !empty($header) && !empty($jsonHeader)) {
            $header = array_merge($header, $jsonHeader);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }
}