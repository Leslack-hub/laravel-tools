<?php

namespace LeslackHub\LaravelTools\Sharding;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class ShardingModel extends Model
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return $this|Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * @param mixed $object
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    protected function forwardCallTo($object, $method, $parameters)
    {
        $shardingTable = $this->sharding(Arr::first($parameters));
        if ($shardingTable !== false) {
            $object->modifyTableName($shardingTable);
        }

        return parent::forwardCallTo($object, $method, $parameters);
    }

    /**
     * @param $attributes
     *
     * @return mixed
     */
    public function sharding($attributes)
    {
        if (isset($attributes[static::SHARDING_COLUMN])) {
            return static::shardingTable($attributes[static::SHARDING_COLUMN]);
        }

        return false;
    }

    /**
     * 分区字段
     */
    const SHARDING_COLUMN = 'id';

    /**
     * @var string 分区表
     */
    private $shardingTable;

    /**
     * @return mixed
     */
    public function getShardingTable()
    {
        return $this->shardingTable;
    }

    /**
     * @param mixed $shardingTable
     */
    public function setShardingTable($shardingTable): void
    {
        $this->shardingTable = $shardingTable;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    abstract public static function shardingTable($value);

    /**
     * @return string|void
     */
    public function getTable()
    {
        if (! empty($this->getShardingTable())) {
            return $this->shardingTable;
        }

        $sharding = $this->sharding($this->attributes);
        return $sharding !== false ? $sharding : parent::getTable();
    }
}
