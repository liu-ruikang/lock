<?php

/**
 * @name EtcdLock
 * @package Lock\Lock
 * @desc Etcd锁对象
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年8月13日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月13日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Lock;

use Lock\Client\EtcdResponse;
use Lock\Exception\LockException;
use Lock\Util\RandomUtil;
use Lock\Util\RenewTask;
use Lock\Util\IRenewalHandler;

class EtcdLock extends BaseLock
{
    protected $client;

    private $hbThread = null;

    public function __construct(object $client, string $clusterName, string $key, bool $reentrant = false)
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
            $etcdResult = $this->client->casVal(self::$key, self::$val, self::$ttl);
            if (!$etcdResult) {
                throw new LockException("etcd cluster Related Error or param error : code" + "", $etcdResult);
            }
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
            $this->client->casDelete(self::$key, self::$val);
            // 释放续租线程
            
            return true;
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

    protected function getLockValue()
    {
        return $this->value; 
    }
}

