## 类

* Captcha   图形验证码
  > captcha()显示图形验证码
  > 
  > checkVerify($code = '')验证图形验证码

* Http    网络请求，get和post，post支持json请求
  > HttpGet($url, $data = []|string, $header = [], $timeout = 30)
  > 
  > get请求

  > HttpPost($url, $data = []|string, $header = [], $timeout = 30)
  >
  > post请求支持json请求，$data参数为string时，会自动转换为json格式

* Aes    对称加解密函数，默认使用aes-128-cbc加密算法
  > encrypt($text, $key, $iv = '')
  > 
  > 对称加密

  > decrypt($text, $key, $iv = '')
  > 
  > 对称解密
  
* RedisLock    redis分布式锁
  > setKeyPrefix($prefix = '')
  > 
  > 设置key前缀
  
  > setRetry($retry = true)
  > 
  > 是否重试
  
  > setRetryCount($retryCount = 3)
  > 
  > 设置重试次数

  > setRetryIntervalUs($retryIntervalUs = 1000000)
  > 
  > 设置重试间隔毫秒数

  > setExpire($expire = 30)
  > 
  > 设置过期时间
  
  > lock($key, $timeout = 30)
  > 
  > 加锁

  > unlock($key)
  > 
  > 解锁

* Runtime    运行时间类
  > getRuntime($name)
  > 
  > 获取运行时间getRuntime获取脚本，函数，代码段的运行时间
* Tools    工具类
  > buildTree($list, $pidKye='pid', $pid = 0, $childKey = 'child')
  > 
  > 数组转树形结构

  > getFolders($dir)
  >
  > 获取指定目录下的所有文件夹名称

  > getClientIp($type = 0)  
  > 
  > 获取客户端ip地址，0获取点分ip，1获取整数ip

  > mobilePartialMask($mobile = '')
  > 
  > 手机号码脱敏，兼容+86格式 

  > getRandomNum($length, $chars = '')  
  > 
  > 获取指定长度的随机数字串

  > getRandomString($length, $chars = '')  
  > 
  > 获取指定长度的随机字符串

  > getOrderNo($length = 8) 
  > 
  > 按日期生成指定长度的流水号

  > arrayCapture($field = '', $array = [])  
  > 
  > 根据输入的filed生成一个包含array中对应key值新的数组

  > timeToText($datetime = '')
  > 
  > 输入一个时间格式，输出文本类型的时间，例如“刚刚”，“1分钟前”，“2小时前”等

  > base64UrlEncode($input = '')
  > 
  > 网络安全的base64编码函数

  > base64UrlDecode($input = '')
  > 
  > 网络安全的base64解码函数