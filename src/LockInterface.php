<?php

/**
 * @name LockInterface
 * @package Lock
 * @desc 锁接口
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

namespace Lock;

interface LockInterface
{
   /**
    * @name acquire
    * @desc 获取锁，如果没得到，不阻塞
    * @param $ttl 过期时间，单位s
    * @param $interval 重试间隔时间，单位s
    * @param $maxRetry 重试次数
    * @return
    */
   public function acquire(int $ttl, int $interval, int $maxRetry);

   /**
    * 释放锁
    */
   public function release();

}

