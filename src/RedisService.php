<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Support\Facades\Redis;

/**
 * @package App\Services\Common
 * @method int         del(array|string $keys)
 * @method string|null dump()
 * @method int         exists()
 * @method int         expire($seconds)
 * @method int         expireat($timestamp)
 * @method array       keys($pattern)
 * @method int         move($db)
 * @method mixed       object($subcommand, $key)
 * @method int         persist()
 * @method int         pexpire($milliseconds)
 * @method int         pexpireat($timestamp)
 * @method int         pttl()
 * @method string|null randomkey()
 * @method mixed       rename($target)
 * @method int         renamenx($target)
 * @method array       scan($cursor, array $options = null)
 * @method array       sort(array $options = null)
 * @method int         ttl()
 * @method mixed       type()
 * @method int         append($value)
 * @method int         bitcount($start = null, $end = null)
 * @method int         bitop($operation, $destkey, $key)
 * @method array|null  bitfield($subcommand, ...$subcommandArg)
 * @method int         bitpos($bit, $start = null, $end = null)
 * @method int         decr()
 * @method int         decrby($decrement)
 * @method string|null get()
 * @method int         getbit($offset)
 * @method string      getrange($start, $end)
 * @method string|null getset($value)
 * @method int         incr()
 * @method int         incrby($increment)
 * @method string      incrbyfloat($increment)
 * @method array       mget(array $keys)
 * @method mixed       mset(array $dictionary)
 * @method int         msetnx(array $dictionary)
 * @method mixed       psetex($milliseconds, $value)
 * @method mixed       set($value, $expireResolution = null, $expireTTL = null, $flag = null)
 * @method int         setbit($offset, $value)
 * @method int         setex($seconds, $value)
 * @method int         setnx($value)
 * @method int         setrange($offset, $value)
 * @method int         strlen()
 * @method int         hdel(array $fields)
 * @method int         hexists($field)
 * @method string|null hget($field)
 * @method array       hgetall()
 * @method int         hincrby($field, $increment)
 * @method string      hincrbyfloat($field, $increment)
 * @method array       hkeys()
 * @method int         hlen()
 * @method array       hmget(array $fields)
 * @method mixed       hmset(array $dictionary)
 * @method array       hscan($cursor, array $options = null)
 * @method int         hset($field, $value)
 * @method int         hsetnx($field, $value)
 * @method array       hvals()
 * @method int         hstrlen($field)
 * @method array|null  blpop(array|string $keys, $timeout)
 * @method array|null  brpop(array|string $keys, $timeout)
 * @method string|null brpoplpush($source, $destination, $timeout)
 * @method string|null lindex($index)
 * @method int         linsert($whence, $pivot, $value)
 * @method int         llen()
 * @method string|null lpop()
 * @method int         lpush(array $values)
 * @method int         lpushx(array $values)
 * @method array       lrange($start, $stop)
 * @method int         lrem($count, $value)
 * @method mixed       lset($index, $value)
 * @method mixed       ltrim($start, $stop)
 * @method string|null rpop()
 * @method string|null rpoplpush($source, $destination)
 * @method int         rpush(array $values)
 * @method int         rpushx(array $values)
 * @method int         sadd(array $members)
 * @method int         scard()
 * @method array       sdiff(array|string $keys)
 * @method int         sdiffstore($destination, array|string $keys)
 * @method array       sinter(array|string $keys)
 * @method int         sinterstore($destination, array|string $keys)
 * @method int         sismember($member)
 * @method array       smembers()
 * @method int         smove($source, $destination, $member)
 * @method string|null spop($count = null)
 * @method string|null srandmember($count = null)
 * @method int         srem($member)
 * @method array       sscan($cursor, array $options = null)
 * @method array       sunion(array|string $keys)
 * @method int         sunionstore($destination, array|string $keys)
 * @method int         zadd(array $membersAndScoresDictionary)
 * @method int         zcard()
 * @method string      zcount($min, $max)
 * @method string      zincrby($increment, $member)
 * @method int         zinterstore($destination, array|string $keys, array $options = null)
 * @method array       zrange($start, $stop, array $options = null)
 * @method array       zrangebyscore($min, $max, array $options = null)
 * @method int|null    zrank($member)
 * @method int         zrem($member)
 * @method int         zremrangebyrank($start, $stop)
 * @method int         zremrangebyscore($min, $max)
 * @method array       zrevrange($start, $stop, array $options = null)
 * @method array       zrevrangebyscore($max, $min, array $options = null)
 * @method int|null    zrevrank($member)
 * @method int         zunionstore($destination, array|string $keys, array $options = null)
 * @method string|null zscore($member)
 * @method array       zscan($cursor, array $options = null)
 * @method array       zrangebylex($start, $stop, array $options = null)
 * @method array       zrevrangebylex($start, $stop, array $options = null)
 * @method int         zremrangebylex($min, $max)
 * @method int         zlexcount($min, $max)
 * @method int         pfadd(array $elements)
 * @method mixed       pfmerge($destinationKey, array|string $sourceKeys)
 * @method int         pfcount(array|string $keys)
 * @method mixed       pubsub($subcommand, $argument)
 * @method int         publish($channel, $message)
 * @method mixed       discard()
 * @method array|null  exec()
 * @method mixed       multi()
 * @method mixed       unwatch()
 * @method mixed       watch()
 * @method mixed       eval($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed       evalsha($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed       script($subcommand, $argument = null)
 * @method mixed       auth($password)
 * @method string      echo ($message)
 * @method mixed       ping($message = null)
 * @method mixed       select($database)
 * @method mixed       bgrewriteaof()
 * @method mixed       bgsave()
 * @method mixed       client($subcommand, $argument = null)
 * @method mixed       config($subcommand, $argument = null)
 * @method int         dbsize()
 * @method mixed       flushall()
 * @method mixed       flushdb()
 * @method array       info($section = null)
 * @method int         lastsave()
 * @method mixed       save()
 * @method mixed       slaveof($host, $port)
 * @method mixed       slowlog($subcommand, $argument = null)
 * @method array       time()
 * @method array       command()
 * @method int         geoadd($longitude, $latitude, $member)
 * @method array       geohash(array $members)
 * @method array       geopos(array $members)
 * @method string|null geodist($member1, $member2, $unit = null)
 * @method array       georadius($longitude, $latitude, $radius, $unit, array $options = null)
 * @method array       georadiusbymember($member, $radius, $unit, array $options = null)
 */
class RedisService
{
    /**
     * @var string
     */
    public $db;

    /**
     * @var string
     */
    protected $key;

    /**
     * redis
     *
     * @var Redis
     */
    public $client;

    /**
     * @var int 过期时间
     */
    public $expire;

    /**
     * 构造函数
     *
     * @param string $db
     * @param string $key
     */
    public function __construct($db, $key = null, $expire = 0)
    {
        $this->db = $db;
        if (! is_null($key)) {
            $this->key = $key;
        }
        $this->client = Redis::connection($db);
        $this->expire = $expire;
    }

    /**
     * 设置
     *
     * @param $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * 获取key
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * 是否设置过期时间
     *
     * @return bool
     */
    public function isSetExpire()
    {
        return $this->client->ttl($this->getKey()) > 0;
    }

    /**
     * 设置过期时间
     *
     * @param $time
     */
    public function setExpireAt($time, $strict = false)
    {
        if ($strict || ! $this->isSetExpire()) {
            $this->client->command('expireat', [$this->getKey(), $time]);
        }
    }

    /**
     * 检查参数
     *
     * @return bool
     */
    public function canExecute()
    {
        return isset($this->client) && ! empty($this->getKey());
    }

    /**
     * 调用方法
     *
     * @param $name
     * @param $arguments
     *
     * @return false|mixed
     */
    public function __call($name, $arguments)
    {
        if (! $this->canExecute()) {
            return false;
        }
        array_unshift($arguments, $this->getKey());
        return tap($this->client->command($name, $arguments), function () {
            // 设置过期时间
            if ($this->expire > 0 && ! $this->isSetExpire()) {
                $this->client->command('expire', [$this->getKey(), $this->expire]);
            }
        });
    }
}
