<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

/**
 * Class RedisHashCount
 *
 * @package App\Http\User\Models
 */
abstract class RedisHashCount
{
    /**
     * @var RedisService;
     */
    public $redis;

    /**
     * 过期时间
     */
    const EXPIRE = 259200;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->redis = $this->setRedis();
    }

    /**
     * 执行
     */
    public function initializationKey()
    {
        if ($this->redis->exists()) {
            return true;
        }
        // 加锁
        $key = $this->redis->getKey();
        if ((bool)Redis::connection($this->redis->db)->setnx($key . '_lock', true)) {
            Redis::connection($this->redis->db)->expire($key . '_lock', 120);
            $dictionary = $this->getDictionary();
            if (! empty($dictionary)) {
                $this->redis->hmset($dictionary);
            }
        }

        return false;
    }

    /**
     * 获取设置值
     *
     * @return mixed
     */
    abstract public function getDictionary();

    /**
     * 设置redis
     *
     * @return mixed
     */
    abstract public function setRedis();

    /**
     * @param $field
     * @param int $value
     *
     * @return int
     */
    public function increase($field, $value = 1)
    {
        $val = $this->getValue($field);
        if ($this->hexist) {
            return (int)$this->redis->hincrby($field, $value);
        }

        return (int)$val;
    }

    /**
     * @param int $value
     *
     * @return mixed
     */
    public function decrease($field, $value = 1)
    {
        $val = $this->getValue($field);
        // 值是否存在
        if ($this->hexist &&
            (($val - $value) >= 0)) {
            return (int)$this->redis->hincrby($field, -$value);
        }
        return (int)$val;
    }

    /**
     * @var bool
     */
    public $hexist = true;

    /**
     * key存在时获取
     *
     * @return mixed
     */
    public function getValue($field)
    {
        $this->initializationKey();
        if (empty($this->redis->hexists($field))) {
            $this->hexist = false;
            $value = $this->getFieldValue($field);
            $this->redis->hset($field, $value);
            return $value;
        }
        return $this->redis->hget($field);
    }

    /**
     * key存在时获取
     *
     * @param $fields
     *
     * @return Collection
     */
    public function getValues($fields)
    {
        $this->initializationKey();
        return collect(array_combine($fields, $this->redis->hmget($fields)));
    }

    /**
     * key存在时设置
     *
     * @return mixed
     */
    public function setValues($dictionary)
    {
        return $this->redis->hmset($dictionary);
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    abstract public function getFieldValue($field);

    /**
     * 设置过期时间
     */
    public function __destruct()
    {
        if ($this->redis->ttl() == -1) {
            $this->redis->expire(static::EXPIRE);
        }
    }
}
