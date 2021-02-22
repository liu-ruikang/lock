<?php

/**
 * @name RedisLock
 * @package Lock\Lock
 * @desc Redis锁对象
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年8月21日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月21日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Lock;

use Lock\Exception\LockException;
use Lock\Util\RandomUtil;
use Lock\Util\RenewTask;
use Lock\Util\IRenewalHandler;

class RedisLock extends BaseLock
{
    protected $client;

    private $hbThread = null;

    private $lockOK = "OK";

    private $releaseOK = 1;

    /**
     * 续租线程
     */
    private $renewalTask;


    public function __construct(object $client, string $key, bool $reentrant = false)
    {
        parent::__construct($key, "", $reentrant);
        $this->client = $client;
    }

    /**
     * @name lock
     * @desc 抢锁
     * @return true-获取锁，false-未获得锁
     */
    public function lock(): bool
    {
        try {
            if ($this->lockOK == $this->client->set(self::$key, self::$val, self::$ttl)) {
                /*
                // 续租线程
                new RenewTask(new IRenewalHandler() {
                    public function callBack()
                    {
                        $result = $this->client->casExist(self::$key, self::$val, self::$ttl);
                        if (!$result) {
                            $this->release();
                        }
                    }
                }, self::$ttl);
                */
                return true;
            } else {
                return false;
            }
        } catch(LockException $e) {
            // error log
            throw $e;
            return false;
        }
    }

    /**
     * @name release
     * @desc 释放锁
     * @return true-释放锁成功，false-释放锁失败
     */
    public function release(): bool
    {
        try {
            if ($this->releaseOK === $this->client->delete(self::$key, self::$val)) {
                // 释放续租线程

                return true;
            }
        } catch(LockException $e) {
            // error log
            throw $e;
            return false;
        }
    }

    public function getClient()
    {
        return $this->client;
    }

}

