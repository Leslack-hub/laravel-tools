<?php

namespace LeslackHub\LaravelTools\Sharding;

use Exception;

/**
 * @package App\Http\Common\Models
 */
class MultiBuilder extends Builder
{
    /**
     * @param $column
     * @param $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function zoneWhere($column, $value, $operator = '=')
    {
        $tableName = $this->getModel()->getTableName($column, $value);
        if (empty($tableName)) {
            throw new Exception('sharding table not exists');
        }
        $this->getModel()->setShardingColumn($column);
        $this->shardingWhere($column, $operator, $value, $tableName);
        return $this;
    }

    /**
     * 更新所有内容
     *
     * @param array $values
     *
     * @return int
     */
    public function updateAll(array $values)
    {
        $count = 0;
        $this->get()->each(function ($model) use ($values, &$count) {
            $model->fill($values);
            if ($model->save()) {
                $count++;
            }
        });

        return $count;
    }

    /**
     * 删除其他分片内容
     *
     * @return int
     */
    public function deleteAll()
    {
        $count = 0;
        $this->get()->each(function ($model) use (&$count) {
            if ($model->delete()) {
                $count++;
            }
        });

        return $count;
    }
}
