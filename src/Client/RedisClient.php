<?php

/**
 * @name RedisClient
 * @package Lock\Client
 * @desc Redis客户端对象
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年8月25日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月25日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Client;

use Lock\Exception\LockException;
use Lock\Util\CurlUtil;
use Lock\Util\RandomUtil;
use Predis\Client;

class RedisClient
{
    protected $client;

    /**
     * 最大重试次数
     */
    const CONNECT_RETRY_COUNT_MAX = 2;

    /**
     * 节点心跳任务
     */
    private $redisHeartbeatTask;

    /**
     * Lua Del Script
     */
    const DEL_SCRIPT = <<<EOT
if redis.call("get",KEYS[1]) == ARGV[1]
then
    return redis.call("del",KEYS[1])
else
    return 0
end
EOT;

    private function __construct(array $config)
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $config['host'],
            'port' => $config['port']
        ]);

        $this->client->auth($config['auth']);
        $this->client->select($config['db']);
    }

    /**
     * @name set 原子加锁
     * @desc 赋值
     * @param key 锁名
     * @param value 锁值
     * @param ttl 锁时间/过期时间
     * @return 返回结果
     */
    public function set(string $key, string $value, int $ttl)
    {
        try {
            return $this->client->set($key, $value, "NX", "EX", $ttl);
        } catch(Exception $e) {
            throw new LockException("Error executing request", $e);
        }
    }

    /**
     * @name delete 原子释放
     * @desc 释放锁
     * @param prevValue 前任锁值
     * @return 返回结果
     */
    public function delete(string $key, string $prevValue)
    {
        try {
            return $this->client->eval(self::DEL_SCRIPT, 1, $key, $prevValue);
        } catch(LockException $e) {
            throw $e;
        }
        return false;
    }

}

