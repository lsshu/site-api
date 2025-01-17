<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SystemDepartment extends SystemModel
{
    protected $fillable = ["parentId", "name", "principal", "phone", "email", "remark", "sort", "status", "guard_name"];

    public function getTable()
    {
        return config('permission.table_names.departments', parent::getTable());
    }
}
