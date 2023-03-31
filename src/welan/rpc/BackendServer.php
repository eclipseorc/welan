<?php
namespace WeLan\Rpc;

class BackendServer
{
    private function callByParams($serviceClass, $method, $request)
    {
        // 构造服务实例名称（此处需要加上根命名空间，否则找不到该类）
        $serviceClass = app()->getNamespace() . '\\service\\' . $serviceClass . 'Service';
        if (is_array($request)) {
            try {
                $class = new \ReflectionClass($serviceClass);
                if (!$class->getMethod($method)) {
                    throw new \Exception('Service class:method definition is invalid. Detail: '. $serviceClass . ' : ' . $method . '. Request: ' . json_encode($request));
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            throw new \Exception('Request is invalid format: ' . json_encode($request));
        }
        $serviceObj = $serviceClass::getInstance();
        if (is_callable([$serviceObj, $method])) {
            return call_user_func_array([$serviceObj, $method], $request);
        } else {
            throw new \Exception('Service:method not found. Detail: ' . $serviceClass . ' : ' . $method);
        }
    }

    /**
     * 调用服务
     * @param array $rawData
     * @return false|mixed
     * @throws \Exception
     * author   oldtom
     * date     2023/3/31 16:01
     */
    public function callService($rawData = [])
    {
        if (!isset($rawData['service']) || empty($rawData['service'])) {
            throw new \Exception('request lost params: service');
        }
        if (!isset($rawData['method']) || empty($rawData['method'])) {
            throw new \Exception('request lost params: method');
        }
        if (!isset($rawData['args'])) {
            throw new \Exception('request lost params: args');
        }
        $service    = trim($rawData['service']);
        $method     = trim($rawData['method']);
        $request    = $rawData['args'];
        try {
            return $this->callByParams($service, $method, $request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}