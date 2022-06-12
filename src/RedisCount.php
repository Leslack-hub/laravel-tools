<?php

namespace LeslackHub\LaravelTools;

/**
 * redis 计数类
 *
 * @package App\Services\Common
 */
abstract class RedisCount
{
    /**
     *  事件
     */
    use EventTrait;

    /**
     * @var RedisService;
     */
    public $redis;

    /**
     * @var mixed 值
     */
    private $value;

    /**
     * @var bool 是否初始化
     */
    public $isInit = false;

    /**
     * 初始化
     */
    public function __construct($expire = null)
    {
        $this->beforeAction();
        $this->redis = $this->setRedis();
        $value = $this->redis->get();
        if (! is_numeric($value)) {
            // 标记已经初始化
            $this->isInit = true;
            $value = $this->initialValue();
            $this->redis->set($value);
            if ($expire) {
                $this->redis->expire($expire);
            }
        }

        $this->setValue(intval($value));
    }

    /**
     * @return mixed
     */
    abstract function setRedis();

    /**
     * @return mixed
     */
    abstract function initialValue();

    /**
     * @param $value
     *
     * @return int
     */
    public function increase($value = 1)
    {
        if ($this->isInit) {
            return $this->getValue();
        }
        return tap($this->redis->incrby($value), function () {
            $this->afterAction();
        });
    }

    /**
     * @param int $value
     *
     * @return mixed
     */
    public function decrease($value = 1)
    {
        if ($this->isInit) {
            return $this->getValue();
        }
        $val = $this->value - $value;
        if ($val >= 0) {
            $this->redis->incrby(-$value);
            $this->setValue($val);
        }

        $this->afterAction();
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
