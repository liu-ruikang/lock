<?php

/**
 * @name LockException
 * @package Lock\Exception
 * @desc 锁异常对象
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年9月15日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年9月15日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\LockException;

class LockException extends Exception
{
    public function __construct(
        string $message,
        int $code,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}

