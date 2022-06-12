<?php

namespace LeslackHub\LaravelTools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class SoftDeleteAbstract extends Model
{
    /**
     * 逻辑删除
     */
    use EventTrait;
    use SoftDeletes;

    /**
     * 新建
     *
     * @return bool
     */
    public function store()
    {
        $this->beforeAction();
        $success = false;
        if (! $this->exists) {
            $success = $this->save();
        } else if ($this->deleted_at !== null) {
            $success = $this->restore();
        }

        $this->afterAction();
        return $success;
    }

    /**
     * 取消
     *
     * @return bool|null
     * @throws null
     */
    public function cancel()
    {
        $this->beforeAction();
        $success = false;
        if ($this->exists &&
            $this->deleted_at === null) {
            $this->delete();
            $success = true;
        }

        $this->afterAction();
        return $success;
    }
}
