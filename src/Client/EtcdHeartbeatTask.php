<?php

/**
 * @name EtcdHeartbeatTask
 * @package Lock\Client
 * @desc etcd 心跳任务：用于节点探活，节点重入
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
use Lock\Util\RandomUtil;
use Lock\Util\CurlUtil;

class EtcdHeartbeatTask
{
    private $etcdClient;

    public function __construct(object $client)
    {
        $this->etcdClient = $client;
    }


}

