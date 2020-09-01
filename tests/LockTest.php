<?php

/**
 * @name LockTest
 * @package Test
 * @desc 测试
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年8月19日 上午10:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月19日    
 * -------------------------------------------------------------------
 * </pre>
 **/

namespace Test;

use PHPUnit\Framework\TestCase;
use Lock\LockFactory;

class LockTest extends TestCase
{
   /**
    * @name build
    * @desc 获取锁客户端
    * @return 锁客户端对象
    */
    public function testBuild()
    {
        $baseUrl = [
            "http://127.0.0.1:2379",
        ];
        $lockClient = LockFactory::etcdSolution()->connections($baseUrl)->clusterName("test-server")->build();
        $lock = $lockClient->newLock("lock_key1");
        echo "lock_key1: " . $lock->acquire(10);
        sleep(1);
        $lock->release();
        $lockClient->close();
        $this->assertTrue(true);
    }

}

