<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Events\Dispatcher;

abstract class ArrayResource
{
    /**
     * @var Dispatcher
     */
    protected static $dispatcher;

    /**
     * trait
     */
    use HasEvents;

    /**
     * 初始化之后
     */
    protected static function booted()
    {
        // 子类调用执行
        if (is_subclass_of(static::class, self::class)) {
            static::setEventDispatcher(app()->get('events'));
        }
    }

    /**
     * @var
     */
    public $resource;

    /**
     * CommentResource constructor.
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
        static::booted();
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        static::fireModelEvent('retrieved');
        foreach ($this->resource as $key => $source) {
            foreach (array_intersect_key($this->callbacks(), ($this->callbacks)) as $name => $callback) {
                $source = $callback($source, ...$this->callbacks[$name]);
            }

            $this->resource[$key] = $source;
        }
        static::fireModelEvent('updated');
        return $this->resource;
    }


    /**
     * 执行函数列表 需要返回该条数据
     *
     * @return mixed
     */
    abstract public function callbacks();

    /**
     * @var array
     */
    public $callbacks = [];

    /**
     * @param $name
     * @param $arguments
     *
     * @return ArrayResource
     */
    public function __call($name, $arguments)
    {
        if (isset($this->callbacks()[$name])) {
            $this->callbacks[$name] = $arguments;
        }
        return $this;
    }
}
