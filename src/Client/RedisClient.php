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

    protected static $_instance;

    /**
     * 最大重试次数
     */
    const CONNECT_RETRY_COUNT_MAX = 2;

    /**
     * 节点心跳任务
     */
    protected $redisHeartbeatTask;

    protected $ifNotExist = "NX";

    protected $secondsExpireTime = "EX";

    /**
     * config array
     */
    protected $config = [
        'host' => 'localhost',
        'port' => 6379,
        'auth' => null,
        'db' => 0,
        'timeout' => 0.0,
        'cluster' => [
            'enable' => false,
            'name' => null,
            'seeds' => [],
        ],
        'options' => [],
    ];

    private function __construct()
    {
        $config = []; // load config.php
        $this->config = array_replace($this->config, $config);
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $this->config['host'],
            'port' => $this->config['port'],
            'timeout' => $this->config['timeout']
        ]);

        if (isset($this->config['auth']) && $this->config['auth'] !== '') {
            $this->client->auth($this->config['auth']);
        }

        if (isset($this->config['db']) && $this->config['db'] > 0) {
            $this->client->select($this->config['db']);
        }

        $options = $this->config['options'] ?? [];
        foreach ($options as $name => $value) {
            $this->client->setOption($name, $value);
        }
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance) || empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
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
            return $this->client->set($key, $value, $this->ifNotExist, $this->secondsExpireTime, (int) max(1, $ttl));
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

        $lua = <<<EOT
if redis.call("get",KEYS[1]) == ARGV[1]
then
    return redis.call("del",KEYS[1])
else
    return 0
end
EOT;

        try {
            return $this->client->eval($lua, 1, $key, $prevValue);
        } catch(LockException $e) {
            throw $e;
        }
        return false;
    }

    private function __clone() {}
}

