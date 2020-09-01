<?php

/**
 * @name RenewTask
 * @package Lock\Util
 * @desc 续租线程
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

namespace Lock\Util;

use Lock\Exception\LockException;

class RenewTask
{
    protected $isRunning = true;

    /**
     * 过期时间，单位s
     */
    protected $ttl;

    /**
     * IRenewalHandler
     */
    protected $call;

    public function __construct(object $call, int $ttl)
    {
        $this->call = $call;
        $this->ttl = $ttl;
    }

    public function run()
    {
        while($this->isRunning)
        {
            try {
                $this->call->callBack();
                sleep($this->ttl / 3);
            } catch(Exception $e) {
                $this->close();
            }
        }
    }

    public function close()
    {
        $this->isRunning = false;
    }

}

