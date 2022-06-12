<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Support\Arr;

/**
 * 事件类
 *
 * @package App\Apptrait
 */
trait EventTrait
{
    /**
     * 事件参数
     *
     * @var array
     */
    private $eventsParams = [];

    /**
     * @param string $name 事件名称
     *
     * @return array
     */
    public function getEventsParam($name)
    {
        return $this->eventsParams[$name] ?? null;
    }

    /**
     * @var array 事件执行结果
     */
    private $eventsResult = [];

    /**
     * @param string $name 事件名称
     *
     * @return array
     */
    public function getEventsResult($name = null)
    {
        if (empty($name)) {
            return $this->eventsResult;
        }
        return $this->eventsResult[$name] ?? null;
    }

    /**
     * @param string $name 事件名称
     * @param array $eventsResult 事件参数
     */
    public function setEventsResult($name, $eventsResult)
    {
        $this->eventsResult[$name] = $eventsResult;
    }

    /**
     * @param string $name 事件名称
     * @param array $eventsParams 事件参数
     */
    public function setEventsParam($name, $eventsParams)
    {
        $this->eventsParams[$name] = $eventsParams;
    }

    /**
     * 定义后 事件
     * [
     *     'nameAfter' => [
     *         className::class, 'functionName'
     *     ],
     *     'nameAfter' => function($obj){};
     * ]
     */
    public function afterEvents()
    {
        return [];
    }

    /**
     * 定义之后事件
     *
     * @return array
     */
    public function beforeEvents()
    {
        return [];
    }

    /**
     * 前事件
     *
     * @param array $names 事件名称
     */
    public function beforeAction($names = [])
    {
        $this->trigger($this->beforeEvents(), $names);
    }

    /**
     * 后事件
     *
     * @param array $names 事件名称
     */
    public function afterAction($names = [])
    {
        $this->trigger($this->afterEvents(), $names);
    }

    /**
     * 执行事件
     *
     * @param $events
     *
     * @return bool
     */
    public function trigger($events, $names)
    {
        if (empty($events)) {
            return false;
        }
        // 删除不执行的事件名称
        if (!empty($names)) {
            $events = array_intersect_key($events, array_flip($names));
        } else {
            $names = $this->onlyEvents;
            if (!empty($names)) {
                $events = Arr::only($events, $names);
            }
        }

        foreach ($events as $name => $event) {
            if (empty($event)) {
                continue;
            }
            if (is_callable($event)) {
                $param = $this->getEventsParam($name) ?? [];
                $this->setEventsResult($name, $event($this, $param));
            }
            if (is_array($event)) {
                $this->setEventsResult($name, call_user_func($event, $this));
            }
        }
        return true;
    }

    /**
     * @var array
     */
    public $onlyEvents = [];
}
