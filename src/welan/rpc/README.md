使用方法：   
0、此框架使用yar扩展，使用前请确认是否已经安装yar扩展，安装方法如下：
    
    当前yar最新版本为2.3.2
    wget https://pecl.php.net/get/msgpack-2.0.3.tgz
    tar zxvf msgpack-2.0.3.tgz  
    cd msgpack-2.0.3  
    /www/server/php/70/bin/phpize
    ./configure --with-php-config=/www/server/php/70/bin/php-config
    make && make install

1、服务端使用
    
    $backend = new BackendServer();
    $server = new \Yar_Server($backend);
    $server->handle();

    服务端使用header参数，可以使用request()->header()来获取客户端传入的YAR_OPT_HEADER参数

2、所有业务service方法继承自BaseService，返回数据统一使用outputData  
3、BackendServer中使用的有tp6的app()函数，此框架只支持tp    
4、远程调用使用    
        
    a、单次调用
        $remote->call($data = [], $url = '', $opt = [])
        其中data为
                [
                    'service'   => 'Base'   // 服务类名
                    'method'    => 'add'    // 服务方法名
                    'args'      => []       // 服务参数，这个数组得是索引数组
                ]
        $url为服务地址
        $opt为yar内置参数，常用的有以下两个
            YAR_OPT_CONNECT_TIMEOUT(int)    例如：[YAR_OPT_CONNECT_TIMEOUT => 1000]
            YAR_OPT_HEADER(array)           例如：[YAR_OPT_HEADER => ["hd1: val", "hd2: val"]]
            YAR_OPT_PACKAGER(int)           例如：[YAR_OPT_PACKAGER => "json"]，默认值为php，可以是php,json,msgpack
            
    b、批量调用
        $remote->callBatch($data = [])
        器中data为
                [
                    'url'               => '',              服务地址
                    'opt'               => [],              例如：[YAR_OPT_TIMEOUT => 1]
                    'service'           => 'Base',          服务类名
                    'method'            => 'add',           服务方法
                    'args'              => [],              服务参数
                    'callback'          => function() {},   回调函数
                    'error_callback'    => function() {}    错误回调函数
                ]