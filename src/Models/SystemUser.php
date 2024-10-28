<?php

namespace Lsshu\Site\Api\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class SystemUser extends Authenticatable implements JWTSubject
{
    use HasRoles, SoftDeletes;

    protected $fillable = ['team_id', 'username', 'nickname', 'avatar', 'password', 'guard_name'];

    public function getTable()
    {
        return config('permission.table_names.users', parent::getTable());
    }

    /**
     * 判断是否是 root 用户
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->username === 'root';
    }

    /***
     * 生成账号密码
     * @param $password
     * @return string
     */
    public function getRootPassword($password)
    {
        return bcrypt($password);
    }

    /***
     * 创建或者更新Root用户
     * @return mixed
     */
    public function initRootUser()
    {
        $root_username = config('site-api.root_username', "root");
        $root_nickname = config('site-api.root_nickname', "Root");
        $root_password = config('site-api.root_password', "root123456");
        $root_guard_name = config('site-api.root_guard_name', "site-api");
        $user = $this->where('username', $root_username)->first();
//        if ($user) {
//            $user->nickname = $root_nickname;
//            $user->password = bcrypt($root_password);
//            $user->guard_name = $root_guard_name;
//            return $user->save();
//        } else {
//            $this->username = $root_username;
//            $this->nickname = $root_nickname;
//            $this->password = bcrypt($root_password);
//            $this->guard_name = $root_guard_name;
//            return $this->save();
//        }


        return $this->updateOrCreate([
            "username" => $root_username
        ], [
            "username" => $root_username,
            "nickname" => $root_nickname,
            "password" => bcrypt($root_password),
            "guard_name" => $root_guard_name,
        ]);
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
