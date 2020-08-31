<?php

/**
 * @name RandomUtil
 * @package Lock\Util
 * @desc 负载均衡工具类
 * @author  Ruikang <liuruikang@360.cn>
 * @date 2020年8月20日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月20日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Util;

use Lock\Exception\LockException;

class RandomUtil
{
    static public function randomInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }

}

