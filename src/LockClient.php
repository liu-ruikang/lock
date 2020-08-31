<?php

/**
 * @name LockClient
 * @package Lock
 * @desc 锁客户端
 * @author  Ruikang <liuruikang@360.cn>
 * @date 2020年8月14日 上午10:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月14日    
 * -------------------------------------------------------------------
 * </pre>
 **/

namespace Lock;

abstract class LockClient
{
   /**
    * @name build
    * @desc 获取锁客户端
    * @return 锁客户端对象
    */
   abstract public function build();

   /**
    * @name newLock
    * @desc 获取锁
    * @param $lockKey 锁名称，全局唯一标识
    * @param $reentrant 是否支持重入
    * @return true-获取锁，false-未获得锁
    */
   abstract public function newLock(string $lockKey, bool $reentrant);

   /**
    * @name close
    * @desc 释放锁
    */
   abstract public function close();

}

