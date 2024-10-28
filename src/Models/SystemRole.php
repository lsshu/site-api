<?php

namespace Lsshu\Site\Api\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemRole extends SpatieRole
{
    use SoftDeletes;

    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }
}
