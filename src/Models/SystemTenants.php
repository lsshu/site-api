<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemTenants extends Model
{
    use SoftDeletes;

    public function getTable()
    {
        return config('permission.table_names.tenants', parent::getTable());
    }
}
