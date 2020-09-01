<?php

/**
 * @name EtcdClient
 * @package Lock\Client
 * @desc Etcd客户端对象：支持etcd集群节点设置，并支持etcd节点自动探活
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

namespace Lock\Client;

use Lock\Exception\LockException;
use Lock\Util\CurlUtil;
use Lock\Util\RandomUtil;

class EtcdClient
{

    /**
     * 最大重试次数
     */
    const CONNECT_RETRY_COUNT_MAX = 2;

    /**
     * 所有etcd节点
     */
    private $allEtcdNodes;

    /**
     * 失联节点列表
     */
    private $brokenEtcdNodes;

    /**
     * 活跃节点列表
     */
    private $availableEtcdNodes;

    /**
     * etcd节点心跳任务
     */
    private $etcdHeartbeatTask;

    private function __construct(array $baseUrl)
    {
        if (empty($baseUrl)) {
            throw new IllegalArgumentException("Lock EtcdClient URL can not be empty ...");
        }
        foreach($baseUrl as $k => $url) {
            if (substr($url, -1, 1) != "/") {
                $baseUrl[$k] = $url . "/";
            }
            $this->allEtcdNodes[] = $baseUrl[$k];
        }
        $this->availableEtcdNodes = $this->allEtcdNodes;
        // 开启心跳线程
        $etcdHeartbeatTask = new EtcdHeartbeatTask($this);
    }

    public static function getInstance(array $baseUrl)
    {
        return new self($baseUrl);
    }

    /**
     * @name casVal
     * @desc 原子赋值
     * @param key 锁名
     * @param value 锁值
     * @param ttl 锁时间/过期时间
     * @return 返回结果
     */
    public function casVal(string $key, string $value, int $ttl)
    {
        try {
            return $this->syncput($key, $value, "", false, $ttl, 0);
        } catch(Exception $e) {
            throw new LockException("Error executing request", $e);
        }
    }

    /**
     * @name casExist
     * @desc 原子刷新
     * @param key 锁名
     * @param value 锁值
     * @param ttl 锁时间/过期时间
     * @return 返回结果
     */
    public function casExist(string $key, string $value, int $ttl)
    {
        try {
            return $this->syncput($key, "", $value, true, $ttl, 0);
        } catch(LockException $e) {
            throw $e;
        }
    }

    /**
     * @name casDelete
     * @desc 原子刷新
     * @param prevValue 前任锁值
     * @return 返回结果
     */
    public function casDelete(string $key, string $prevValue)
    {
        try {
            return $this->syncdelete($key, $prevValue, 0);
        } catch(LockException $e) {
            throw $e;
        }
    }

    /**
     * @name syncput
     * @desc 同步put请求
     * @param key 锁名
     * @param value 锁值
     * @param prevValue 前任锁值
     * @param exist 是否存在
     * @param ttl 锁时间/过期时间
     * @param connectRetryCount 重试次数，最大值为2
     * @return etcd响应结果
     */
    private function syncput(string $key, string $value, string $prevValue, bool $exist, int $ttl, int $connectRetryCount)
    {
        $url = !$prevValue ? "v2/keys/" . $key : "v2/keys/" . $key . "?prevValue=" . $prevValue;

        $commonds = [];
        $url = $this->getRandomAvailableEtcdNode() . $url;

        if ($value != "") {
            $commonds["value"] = $value;
        }

        if ($exist !== null) {
            $prevExist = $exist ? "true" : "false";
            $commonds["prevExist"] = $prevExist;

            if ($exist === true) {
                $commonds["refresh"] = "true";
            }
        }

        if ($ttl > 0) {
            $commonds["ttl"] = $ttl;
        }

        try {
            $json = CurlUtil::execCurl($url, "PUT", [], $commonds);
            return $json;
        } catch(RuntimeException $e) {
            if ($connectRetryCount > CONNECT_RETRY_COUNT_MAX) {
                throw new LockException("Lock etcd node cannot connect error", $e);
            }
            $connectRetryCount++;
            return $this->syncput($key, $value, $prevValue, $exist, $ttl, $connectRetryCount);
        } catch(Exception $e) {
            throw new LockException("sync http request : [error sync http request exception]", $e);
        }
    }

    /**
     * @name syncdelete
     * @desc 同步delete请求
     * @param key 锁名
     * @param prevValue 前任锁值
     * @param connectRetryCount 重试次数，最大值为2
     * @return etcd响应结果
     */
    private function syncdelete(string $key, string $prevValue, int $connectRetryCount)
    {
        $url = "v2/keys/" . $key . "?prevValue=" . $prevValue;

        $url = $this->getRandomAvailableEtcdNode() . $url;

        try {
            $json = CurlUtil::execCurl($url, "DELETE");
            return $json;
        } catch(RuntimeException $e) {
            if ($connectRetryCount > CONNECT_RETRY_COUNT_MAX) {
                throw new LockException("Lock etcd node cannot connect error", $e);
            }
            $connectRetryCount++;
            return $this->syncdelete($key, $prevValue, $connectRetryCount);
        } catch(Exception $e) {
            throw new LockException("sync http request : [error sync http request exception]", $e);
        }
    }

    /**
     * @name getRandomAvailableEtcdNode
     * @desc 获取etcd随机活跃节点
     * @return etcd节点
     */
    private function getRandomAvailableEtcdNode()
    {
        if (empty($this->availableEtcdNodes)) {
            //Log.error("Lock all etcd nodes has broken, use  var [allEtcdNodes] instead...");
            $this->availableEtcdNodes = $this->allEtcdNodes;
        }
        return $this->availableEtcdNodes[RandomUtil::randomInt(0, count($this->availableEtcdNodes) - 1)];
    }

    private function getAllEtcdNodes()
    {
        return $this->allEtcdNodes;
    }

    private function getAvailableEtcdNodes()
    {
        return $this->availableEtcdNodes;
    }

    private function getBrokenEtcdNodes()
    {
        return $this->brokenEtcdNodes;
    }

}

