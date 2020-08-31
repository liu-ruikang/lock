<?php

/**
 * @name EtcdSolution
 * @package Lock\Lock
 * @desc Etcd锁对象
 * @author  Ruikang <liuruikang@360.cn>
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

use Lock\LockInterface;
use Lock\LockClient;
use Lock\Lock\EtcdLock;
use Lock\Client\EtcdClient;
use Lock\Exception\LockException;

class EtcdSolution extends LockClient
{
    protected $etcdClient;

    /**
     * etcd节点列表
     */
    private $baseUrl;

    /**
     * 集群名
     */
    private $clusterName;

    private $timeout;

    /**
     * @name connections
     * @desc 
     * @param array $baseUrl
     * @return
     */
    public function connections(array $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @name timeout
     * @desc 单位ms，设置后调用acquire方法最大等待时间
     * @param int $timeout
     * @return
     */
    public function timeout(int $timeout)
    {
        if ($timeout < 5 || $timeout > 2000) {
            throw new InvalidArgumentException("timeout 取值区间【5, 2000】");
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @name clusterName
     * @param string $name
     * @return
     */
    public function clusterName(string $clusterName)
    {
        $this->clusterName = $clusterName;
        return $this;
    }

    /**
     * @name build
     * @return
     */
    public function build()
    {
        if (!empty($this->baseUrl)) {
            $this->etcdClient = EtcdClient::getInstance($this->baseUrl);
        }
        if ($this->clusterName == "") {
            throw new LockException("clusterName is must param, please add clusterName param");
        }

        return $this;
    }

    /**
     * @name newLock
     * @desc Etcd锁实例
     * @param array $baseUrl
     * @return
     */
    public function newLock(string $lockKey, bool $reentrant = false)
    {
        return new EtcdLock($this->etcdClient, $this->clusterName, $lockKey, $reentrant);
    }

   /**
    * @name close
    * @desc 释放锁
    */
   public function close() {}

}

