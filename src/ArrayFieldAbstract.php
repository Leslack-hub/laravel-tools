<?php

namespace LeslackHub\LaravelTools;

abstract class ArrayFieldAbstract
{
    /**
     * @var array
     */
    public $data = [];

    /**
     * SectionService constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @var array 修改字段属性
     */
    public $modifyFields = [];

    /**
     * @var array 删除的字段
     */
    public $dropFields = [];

    /**
     * @var array 只修改
     */
    public $onlyFields = [];

    /**
     * 设置字段值
     *
     * @return array
     */
    public function setFields()
    {
        return [];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = array_merge($this->setFields(), $this->modifyFields);
        if (! empty($this->onlyFields)) {
            return array_intersect_key($fields, array_flip($this->onlyFields));
        }
        // 删除字段
        if (! empty($this->dropFields)) {
            $fields = array_diff_key($fields, array_flip($this->dropFields));
        }
        return $fields;
    }

    /**
     * 增加字段
     *
     * @param string $name 字段名称
     * @param callable|mixed $value 回调函数 或者值
     */
    public function addField($name, $value)
    {
        $this->modifyFields[$name] = $value;
    }

    /**
     * 转换数组
     */
    public function conversion()
    {
        $result = [];
        foreach ($this->data as $key => $item) {
            if ($this->beforeCheck($item) &&
                ($item = $this->setFieldValue($item)) &&
                $this->afterCheck($item)) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @var
     */
    protected $item;

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item): void
    {
        $this->item = $item;
    }

    /**
     * 后删除字段
     *
     * @return array
     */
    abstract public function afterDeletedField();

    /**
     * 检测
     *
     * @return bool
     */
    abstract public function beforeCheck($item);

    /**
     * 验证
     *
     * @param array $item
     *
     * @return bool
     */
    abstract public function afterCheck($item);

    /**
     * 设置值
     *
     * @return array
     */
    protected function setFieldValue($info)
    {
        $arr = $info;
        $this->setItem($info);
        foreach ($this->fields() as $field => $value) {
            $arr[$field] = is_callable($value) ? $value($info) : $value;
        }
        return array_diff_key($arr, array_flip($this->afterDeletedField()));
    }
}
