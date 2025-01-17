<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SystemTenantPackages extends SystemModel
{
    protected $fillable=["name","remark","status","guard_name"];

    public function getTable()
    {
        return config('permission.table_names.tenant_packages', parent::getTable());
    }
}
