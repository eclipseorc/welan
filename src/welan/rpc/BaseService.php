<?php
namespace WeLan\Rpc;

/**
 * 服务基类
 * Class BaseService
 * @package WeLan\Rpc
 * author  oldtom
 * date    2023/3/31 15:46
 */
class BaseService
{
    protected static $instance;

    private function __construct()
    {
        $this->init();
    }

    /**
     * 重写初始化函数
     * author   oldtom
     * date     2023/3/31 15:42
     */
    public function init()
    {

    }

    /**
     * 单例
     * @return mixed
     * author   oldtom
     * date     2023/3/31 15:47
     */
    public static function getInstance()
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 格式化返回数据
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return array
     * author   oldtom
     * date     2023/3/31 15:46
     */
    public function outputData($data = [], $code = 200, $msg = '')
    {
        return [
            'code'  => $code,
            'msg'   => $msg,
            'data'  => $data
        ];
    }
}