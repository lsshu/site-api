<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Lsshu\Site\Api\Models\Root;

class RootsController extends Controller
{
    protected string $model = Root::class;

    /***
     * 处理提交的数据 处理密码
     * @param Request $request
     * @return mixed|void
     */
    public function input(Request $request)
    {
        $data = $request->only(['team_id', 'name', 'password', 'guard_name']);
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }
}
