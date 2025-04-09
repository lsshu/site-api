<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Lsshu\Site\Api\Models\SystemUser;

class UserController extends SiteApiController
{
    protected string $model = SystemUser::class;

    /***
     * 处理提交的数据 处理密码
     * @param Request $request
     * @return mixed|void
     */
    public function input(Request $request)
    {
        $data =  $request->all();
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }
}
