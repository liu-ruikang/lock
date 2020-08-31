<?php

/**
 * @name LockFactory
 * @package Lock
 * @desc 锁工厂类
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

use Lock\Lock\RedisSolution;
use Lock\Lock\EtcdSolution;

class LockFactory
{
    /**
     * 获取etcd锁方案实例
     */
    public static function etcdSolution() {
        return new EtcdSolution();
    }
 
    /**
     * 获取redis锁方案实例
     */
    public static function redisSolution() {
        return new RedisSolution();
    }
}

