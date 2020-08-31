<?php

/**
 * @name BaseLock
 * @package Lock\Lock
 * @desc 锁基类
 * @author  Ruikang <liuruikang@360.cn>
 * @date 2020年8月12日 上午10:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月12日    
 * -------------------------------------------------------------------
 * </pre>
 **/

namespace Lock\Lock;

use Lock\LockInterface;

class BaseLock implements LockInterface
{

    /**
     * 默认过期时间，单位秒
     */
    const DEFAULT_SECONDS = 10;

    /**
     * 默认缓存值
     */
    const DEFAULT_VAL = "1";

    /**
     * 锁名
     */
    protected static $key;

    /**
     * 锁值
     */
    protected static $val;

    /**
     * 过期时间，单位s
     */
    protected static $ttl;

    /**
     * 是否支持锁重入
     */
    protected static $reentrant;

    /**
     * 是否已经持有锁
     */
    protected static $hold = false;

    public function __construct(string $key, string $val, bool $reentrant)
    {
        self::$key = $key;
        self::$val = $val;
        self::$ttl = self::DEFAULT_SECONDS;
        self::$reentrant = $reentrant;
    }

    /**
     * @name acquire
     * @desc 获取锁，如果没得到，不阻塞
     * @param ttl 过期时间，单位s
     * @param interval 重试间隔时间，单位s
     * @param $maxRetry 重试次数
     * @return
     */
    public function acquire(int $ttl, int $interval = 1, int $maxRetry = 3): bool
    {
        if (!$this->getClient() || !self::$key) {
            // 空锁
            return false;
        } else if (self::$reentrant) {
            // 可重入
            return true;
        }
        self::$ttl = ($ttl < 0 ? self::DEFAULT_SECONDS : $ttl);
        try {
            if (!self::lock()) {
                if ($maxRetry > 0) {
                    sleep($interval > 0 ? $interval : 1);
                    return $this->acquire($ttl, $interval, $maxRetry - 1);
                }
            }
            return true;
        } catch(Exception $e) {
            if ($maxRetry > 0) {
                sleep($interval > 0 ? $interval : 1);
                return $this->acquire($ttl, $interval, $maxRetry - 1);
            }
            return false;
        }
    }

    /**
     * @name lock
     * @desc 抢锁
     * @return
     */
    public function lock() {}

    /**
     * @name release
     * @desc 释放锁
     * @return
     */
    public function release() {}

    /**
     * @name getClient
     * @desc 获取方案客户端
     */
    public function getClient() {}
}

