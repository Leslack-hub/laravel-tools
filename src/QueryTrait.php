<?php

namespace LeslackHub\LaravelTools;

use Exception;
use Illuminate\Support\Str;

trait QueryTrait
{
    /**
     * 初始化
     */
    public static function bootQueryTrait()
    {
        if (static::isSelectScene('default')) {
            static::addGlobalScope('selectDefault', function ($query) {
                $query->select(static::$selectScenario['default']);
            });
        }
        if (static::isWhereScene('default')) {
            static::addGlobalScope('whereDefault', function ($query) {
                $query->where(static::$whereScene['default']);
            });
        }
    }

    /**
     * @param $query
     */
    public function scopeScene($query, $scene)
    {
        if (static::isSelectScene($scene)) {
            $query->addSelect(static::$selectScenario[$scene]);
        }
        if (static::isWhereScene($scene)) {
            $query->where(static::$whereScenario[$scene]);
        }
    }

    /**
     * @param$scene
     *
     * @return bool
     */
    public static function isWhereScene($scene): bool
    {
        return ! empty(static::$whereScenario) && isset(static::$whereScenario[$scene]);
    }

    /**
     * @param$scene
     *
     * @return bool
     */
    public static function isSelectScene($scene): bool
    {
        return ! empty(static::$selectScenario) && isset(static::$selectScenario[$scene]);
    }

    /**
     * @param $scene
     * @param $value
     */
    public static function addWhereScene($scene, $value)
    {
        data_set(static::$whereScenario, $scene, $value);
    }

    /**
     * @param $scene
     * @param $value
     */
    public static function addSelectScene($scene, $value)
    {
        data_set(static::$selectScenario, $scene, $value);
    }

    /**
     * @param $type
     *
     * @return mixed
     * @throws Exception
     */
    public function getTypeModel($type)
    {
        $modelName = Str::of($type)->camel() . 'Model';
        if (! isset($this->getRelations()[$type])) {
            $maps = constant(get_class($this) . '::' . strtoupper($type) . '_MAPS');
            if (! isset($maps[$this->{$type}])) {
                throw new Exception($type . ' can not convert');
            }
            $typeModel = new $maps[$this->{$type}]();
            if (method_exists($typeModel, 'initialization')) {
                $typeModel->initialization($this);
            }
            $this->setRelation($modelName, $typeModel);
        } else {
            $typeModel = $this->getRelation($modelName);
        }

        return $typeModel;
    }
}
