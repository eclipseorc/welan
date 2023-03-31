<?php
namespace WeLan\Lib;

/**
 * redis锁
 * Class RedisLock
 * @package WeLan\Lib
 * author  oldtom
 * date    2023/3/30 18:46
 */
class RedisLock
{
    // redis前缀
    private $_keyPrefix         = "RedisLock:";

    // 重试次数，0无限重试
    private $_retryCount        = 0;
    // 获取锁失败后重试等待的时间，微秒， 1ms=1000us;
    private $_retryIntervalUs   = 100000;

    // 获取锁失败后，是否重试
    private $_retry             = true;
    // 锁的超时时间，防止死锁发生
    private $_expire            = 0;
    // redis对象
    private $_redis             = null;
    // 锁key
    private $_lockKey           = '';

    public function __construct($redisHandle = null, $keyPrefix = 'RedisLock:', $expire = 10, $retry = true, $retryCount = 0, $retryIntervalUs = 100000)
    {
        $this->_redis           = $redisHandle;
        $this->_expire          = $expire;
        $this->_retry           = $retry;
        $this->_retryCount      = $retryCount;
        $this->_retryIntervalUs = $retryIntervalUs;
        $this->setKeyPrefix($keyPrefix);
    }

    /**
     * 加锁
     * @param $key
     * @return bool
     * author   oldtom
     * date     2023/3/30 18:01
     */
    public function lock($key)
    {
        $this->_lockKey = $this->_keyPrefix . $key;
        $count          = 0;
        while (true) {
            $nowTime    = self::maritime();
            $lockValue  = self::maritime() + $this->_expire;
            $lock       = $this->_redis->setnx($this->_lockKey, $lockValue);
            if (!empty($lock) || ($this->_redis->get($this->_lockKey) < $nowTime && $this->_redis->getset($this->_lockKey, $lockValue) < $nowTime)) {
                $this->_redis->expire($this->_lockKey, $this->_expire);
                return true;
            } elseif ($this->_retry && (($this->_retryCount > 0 && ++$count < $this->_retryCount) || ($this->_retryCount == 0))) {
                usleep($this->_retryIntervalUs);
            } else {
                break;
            }
        }
        return false;
    }

    /**
     * 释放锁
     * @param $lockKey
     * author   oldtom
     * date     2023/3/30 18:01
     */
    public function unlock($lockKey)
    {
        $this->_lockKey = $this->_keyPrefix . $lockKey;
        if ($this->_redis->ttl($this->_lockKey)) {
            $this->_redis->del($this->_lockKey);
        }
    }

    /**
     * 设置key前缀
     * @param string $keyPrefix
     * @return $this
     * author   oldtom
     * date     2023/3/30 18:02
     */
    public function setKeyPrefix($keyPrefix = '')
    {
        $this->_keyPrefix = $keyPrefix == rtrim($keyPrefix, ':') ? $keyPrefix . ':' : $keyPrefix;
        return $this;
    }

    /**
     * 设置是否重试
     * @param bool $retry
     * @return $this
     * author   oldtom
     * date     2023/3/30 18:02
     */
    public function setRetry($retry = true)
    {
        $this->_retry = $retry;
        return $this;
    }

    /**
     * 设置过期时间
     * @param int $expire
     * @return $this
     * author   oldtom
     * date     2023/3/30 18:02
     */
    public function setExpire($expire = 0)
    {
        $this->_expire = $expire;
        return $this;
    }

    /**
     * 设置重试次数
     * @param int $retryCount
     * @return $this
     * author   oldtom
     * date     2023/3/30 18:03
     */
    public function setRetryCount($retryCount = 3)
    {
        $this->_retryCount = $retryCount;
        return $this;
    }

    /**
     * 设置重试间隔毫秒数
     * @param int $retryIntervalUs
     * @return $this
     * author   oldtom
     * date     2023/3/30 18:03
     */
    public function setRetryIntervalUs($retryIntervalUs = 100000)
    {
        $this->_retryIntervalUs = $retryIntervalUs;
        return $this;
    }

    private static function maritime()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
}