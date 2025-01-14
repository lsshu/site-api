<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemTenants extends Model
{
    use SoftDeletes;
    protected $fillable=["name","packageId","contactName","contactMobile","accountCount","expireTime","website","remark","status","guard_name"];

    public function getTable()
    {
        return config('permission.table_names.tenants', parent::getTable());
    }
}
