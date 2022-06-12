<?php

namespace LeslackHub\LaravelTools\Sharding;

class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * @param $column
     * @param $value
     * @param string $operator
     * @param string $tableName
     *
     * @return Builder
     */
    public function shardingWhere($column, $value, $operator = '=', $tableName = null)
    {
        // 默认为单字段分表
        if (empty($tableName)) {
            $tableName = call_user_func([$this->getModel(), 'shardingTable'], $value);
        }
        $this->modifyTableName($tableName);
        parent::where($column, $operator, $value);
        return $this;
    }

    /**
     * 修改表名称
     *
     * @param $tableName
     */
    public function modifyTableName($tableName)
    {
        $this->getQuery()->from($tableName);
        $this->getModel()->setTable($tableName);
        $this->getModel()->setShardingTable($tableName);
    }

    /**
     * @param array|Closure|\Illuminate\Database\Query\Expression|string $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @param string $boolean
     *
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        parent::where($column, $operator, $value, $boolean);
        if ($this->getModel()->getShardingTable() === null) {
            $this->checkWhereShardingColumn($this->query);
        }

        return $this;
    }

    /**
     * 查看where中是否有分区字段
     *
     * @param $query
     */
    public function checkWhereShardingColumn($query)
    {
        $shardingColumn = constant(get_class($this->getModel()) . '::SHARDING_COLUMN');
        foreach ($query->wheres as $where) {
            if ($where['type'] == 'Basic') {
                $start = strpos($where['column'], '.');
                $column = $start === false ? $where['column'] : substr($where['column'], $start + 1);
                if (strcmp($column, $shardingColumn) === 0) {
                    $this->modifyTableName(
                        $this->getModel()->shardingTable($where['value'])
                    );
                    break;
                }
            } else if ($where['type'] == 'Nested') {
                $this->checkWhereShardingColumn($where['query']);
            }
        }
    }

    /**
     *
     * @param \Closure|\Illuminate\Database\Query\Builder|string $table
     * @param string|null $as
     *
     * @return $this
     */
    public function from($table, $as = null)
    {
        $this->modifyTableName($table);
        return parent::from($table);
    }
}
