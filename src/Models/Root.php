<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Root extends Authenticatable implements JWTSubject
{
    use HasRoles, SoftDeletes;

    protected $fillable = ['team_id', 'name', 'password', 'guard_name'];

    public function getTable()
    {
        return config('permission.table_names.roots', parent::getTable());
    }

    /**
     * 判断是否是 root 用户
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->name === 'root';
    }

    /**
     * 获取将存储在JWT主题声明中的标识符。
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 返回一个键值数组，其中包含要添加到JWT的任何自定义声明。
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
