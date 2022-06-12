<?php

namespace LeslackHub\LaravelTools\Sharding;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

abstract class MultiShardingModel extends ShardingModel
{
    /**
     * 分区字段
     */
    const SHARDING_COLUMNS = [];

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return MultiBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new MultiBuilder($query);
    }

    /**
     * @param $column
     * @param $val
     *
     * @return mixed
     */
    public function getTableName($column, $val)
    {
        return call_user_func([static::class, 'shardingBy' . Str::studly($column)], $val);
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = [])
    {
        if (! empty($this->shardingColumn)) {
            static::flushEventListeners();
        }

        $dirty = $this->getDirty();
        if ($this->exists &&
            $changes = array_intersect_key($dirty, static::SHARDING_COLUMNS)) {
            $originals = $this->getOriginal();
            // 删除成功事件
            foreach ($changes as $key => $value) {
                $oldTableName = $this->getTableName($key, $originals[$key]);
                if (empty($oldTableName)) {
                    continue;
                }

                $condition = [];
                foreach (static::SHARDING_COLUMNS[$key]['condition'] as $column) {
                    $condition[$column] = $originals[$column];
                }
                if (! empty($condition)) {
                    static::registerModelEvent(
                        'changed_sharding_column:' . $key,
                        function ($tableName) use ($oldTableName, $condition) {
                            // 为空或相等
                            if (empty($oldTableName) ||
                                $tableName === $oldTableName) {
                                return false;
                            }
                            return DB::table($oldTableName)->where($condition)->delete();
                        }
                    );
                }
            }
        }

        $result = parent::save($options);
        $this->runSharding($this->attributes, function ($tableInfo, $rule) {
            try {
                return DB::table($tableInfo['table'])->updateOrInsert($tableInfo['condition'], $this->getFill($rule['fill']));
            } catch (Exception $e) {
                Log::debug($e->getMessage());
            }

            return false;
        });

        return $result;
    }

    /**
     * @var
     */
    protected $shardingColumn;

    /**
     * @return mixed
     */
    public function getShardingColumn()
    {
        return $this->shardingColumn;
    }

    /**
     * @param mixed $shardingColumn
     */
    public function setShardingColumn($shardingColumn): void
    {
        $this->shardingColumn = $shardingColumn;
    }

    /**
     * @param $attributes
     * @param $callback
     */
    public function runSharding($attributes, $callback)
    {
        $columns = $this->getShardingColumns();
        foreach ($columns as $key => $rule) {
            // 分区表和查询条件
            $tableInfo = [];
            if (! isset($attributes[$key]) ||
                empty($attributes[$key])) {
                $methodName = 'shardingFailBy' . Str::studly($key);
                if (method_exists($this, $methodName)) {
                    $tableInfo = $this->{$methodName}($attributes);
                }
            } else {
                $tableInfo['table'] = $this->getTableName($key, $attributes[$key]);
                $tableInfo['condition'] = $this->getCondition($rule['condition']);
            }
            if (empty($tableInfo['table'] ||
                empty($tableInfo['condition']))) {
                continue;
            }
            $callback($tableInfo, $rule);
            // 触发后事件
            static::$dispatcher->dispatch(
                'eloquent.changed_sharding_column:' . $key . ': ' . static::class, $tableInfo['table']
            );
        }
    }

    /**
     * @return bool|void|null
     * @throws \Exception
     */
    public function delete()
    {
        if (! empty($this->shardingColumn)) {
            static::flushEventListeners();
        }
        $result = parent::delete();
        $this->runSharding($this->attributes, function ($tableInfo, $rule) {
            // 直接删除
            if (! isset($this->forceDeleting) ||
                $this->forceDeleting) {
                DB::table($tableInfo['table'])->where($tableInfo['condition'])->delete();
            } else {
                DB::table($tableInfo['table'])->updateOrInsert($tableInfo['condition'], $this->getFill($rule['fill']));
            }
        });
        return $result;
    }

    /**
     * @param $column
     *
     * @return array|false
     */
    public function getCondition($column)
    {
        $condition = [];
        foreach ($column as $value) {
            if (! isset($this->attributes[$value]) ||
                empty($this->attributes[$value])) {
                return false;
            }
            $condition[$value] = $this->attributes[$value];
        }
        return $condition;
    }

    /**
     * @param $fill
     *
     * @return array
     */
    protected function getFill($fill): array
    {
        return array_intersect_key($this->attributes, array_flip($fill));
    }

    /**
     * @return array
     */
    protected function getShardingColumns(): array
    {
        $columns = static::SHARDING_COLUMNS;
        if (! empty($this->shardingColumn)) {
            unset($columns[$this->shardingColumn]);
        } else {
            unset($columns[static::SHARDING_COLUMN]);
        }

        return $columns;
    }

    /**
     * @param $attributes
     * @param $exists
     *
     * @return MultiShardingModel
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);
        $model->setShardingColumn($this->getShardingColumn());
        $model->setShardingTable($this->getShardingTable());
        return $model;
    }
}
