<?php
namespace WeLan\Rpc;

use Exception;

/**
 * 远程调用服务
 * Class Remote
 * @package WeLan\Rpc
 * author  oldtom
 * date    2023/3/31 15:47
 */
class Remote
{
    private static $result;
    private $rawData;
    private $api;

    /**
     * 构造函数
     * Remote constructor.
     * @param string $apiUrl    远程服务地址
     * @throws Exception
     */
    public function __construct($apiUrl = '')
    {
        if (!extension_loaded('yar')) {
            throw new Exception("extension yar not loaded");
        }
        $this->api      = $apiUrl;
    }

    /**
     * 调用服务
     * @param array $data    调用参数
     * @param string $url   远程服务地址
     * @param array $opts   yar参数
     * @return array
     * author   oldtom
     * date     2023/3/31 15:17
     */
    public function call($data = [], $url = '', $opts = [])
    {
        if (empty($url)) {
            $url = $this->api;
        }
        $client = new \Yar_Client($url);
        if (!empty($opts)) {
            foreach ($opts as $key => $value) {
                $client->setOpt($key, $value);
            }
        }
        try {
            $result = $client->callService($data);
        } catch (Exception $e) {
            $result = [
                'code'  => $e->getCode(),
                'msg'   => $e->getMessage(),
                'data'  => []
            ];
        }
        return $result;
    }

    /**
     * 批量调用服务
     * @param array $data   调用参数
     * @return mixed
     * author   oldtom
     * date     2023/3/31 15:36
     */
    public function callBatch($data = [])
    {
        foreach ($data as $key => $value) {
            $uri            = isset($value['url']) ? $value['url'] : $this->api;
            $opt            = isset($value['opt']) && is_array($value['opt']) ? $value['opt'] : [];
            $callback       = isset($value['callback']) ? $value['callback'] : null;
            $errorCallback  = isset($value['error_callback']) ? $value['error_callback'] : null;
            $params         = [
                'service'   => $value['service'],
                'method'    => $value['method'],
                'args'      => isset($value['args']) ? $value['args'] : [],
            ];

            $this->rawData[$key + 1] = $params;
            \Yar_Concurrent_Client::call($uri, 'callService', [$params], $callback, $errorCallback, $opt);
        }
        \Yar_ConCurrent_Client::loop([$this, 'callback'], [$this, 'errorCallback']);
        // 重置请求
        \Yar_ConCurrent_Client::reset();
        $result = self::$result;
        // 重置结果
        self::$result = null;
        return $result;
    }

    /**
     * 回调函数
     * @param $retVal
     * @param $callInfo
     * @return bool
     * author   oldtom
     * date     2023/3/31 15:34
     */
    private function callback($retVal, $callInfo)
    {
        if (empty($callInfo)) {
            return true;
        }
        self::$result[$callInfo['sequence']] = $retVal;
        return true;
    }

    /**
     * 错误回调
     * @param $type
     * @param $error
     * @param $callInfo
     * author   oldtom
     * date     2023/3/31 15:35
     */
    private function errorCallback($type, $error, $callInfo)
    {
        if (is_array($error)) {
            self::$result[$callInfo['sequence']]['code']  = $error['code'] ? $error['code'] : 600;
            self::$result[$callInfo['sequence']]['msg']   = $error['message'];
            self::$result[$callInfo['sequence']]['trace'] = $error['file'] . ' In line:' . $error['line'];
        } else {
            self::$result[$callInfo['sequence']]['code']  = '600';
            self::$result[$callInfo['sequence']]['msg']   = $error;
            self::$result[$callInfo['sequence']]['trace'] = '';
        }
        self::$result[$callInfo['sequence']]['data']    = '';
    }
}