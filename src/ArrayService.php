<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Support\Arr;

/**
 * Class ArrayService
 *
 * @package App\Services\Common
 */
class ArrayService
{
    /**
     * @var array
     */
    private $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }

    private $keys;

    /**
     * ArrayService constructor.
     */
    public function __construct($key, ...$values)
    {
        $this->keys = Arr::wrap($key);
        // 添加
        foreach ($values as $value) {
            $this->push($value);
        }
    }

    /**
     * @param $values
     * @param  $name
     */
    public function push($values, $name = null)
    {
        $keyLength = count($this->keys);
        $valueLength = count($values);
        if ($keyLength > $valueLength) {
            $values = array_pad($values, $keyLength - $valueLength, null);
        } else if ($keyLength < $valueLength) {
            array_splice($values, $valueLength - $keyLength);
        }
        $arr = array_combine($this->keys, $values);
        if (empty($name)) {
            $this->data[] = $arr;
        } else {
            $this->data[$name] = $arr;
        }
    }

    /**
     * @param $name
     *
     * @return array|mixed
     */
    public function getValue($name, $default = null)
    {
        return data_get($this->getData(), $name, $default);
    }

    /**
     * @param $groupName
     *
     * @return array
     */
    public function execValuesCallable($groupName)
    {
        $groups = data_get($this->getData(), $groupName);
        if (is_array($groups)) {
            foreach ($groups as $key => $group) {
                if (is_callable($group)) {
                    $this->data[$groupName][$key] = $group($groupName);
                }
            }
        }
        return is_callable($groups) ? $groups($groupName) : null;
    }

    /**
     * @param $key
     * @param array $values
     */
    public function pushKey($key, array $values = [], $name = null)
    {
        $name === null ? array_push($this->keys, $key) : $this->keys[$name] = $key;
        if ($values && !$name) {
            $name = array_search($key, $this->keys);
        }

        $this->data[$name] = $values;
    }
}
