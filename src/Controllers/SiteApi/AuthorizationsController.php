<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{
    protected string $guard_name = "site-api";

    public function login()
    {
        return $this->errorResponse(401, '授权过期');
    }

    /**
     * 获取 token
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $credentials = [];
        $credentials['name'] = $request->name;
        $credentials['password'] = $request->password;
        $credentials['guard_name'] = $this->guard_name;

        if (!$token = Auth::guard($this->guard_name)->attempt($credentials)) {
            return $this->errorResponse(401, '用户名或密码错误', 1001);
        }
        return $this->respondWithToken($token, $credentials['name']);
    }

    /**
     * 刷新 token
     *
     * @return mixed|void
     */
    public function refresh()
    {
        try {
            $token = Auth::guard($this->guard_name)->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'token 无法更新', 1002);
        }
        return $this->respondWithToken($token);
    }

    /**
     * 删除 token
     *
     * @return \Illuminate\Http\Response|void
     */
    public function logout()
    {
        try {
            Auth::guard($this->guard_name)->logout();
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'token 已过期');
        }
        // 删除成功，返回 204， 没有其他内容返回
        return response()->noContent();
    }

    /**
     * 返回 token
     *
     * @param $token
     * @param $name
     * @return mixed
     */
    public function respondWithToken($token, $name)
    {
        $expiresIn = Auth::guard($this->guard_name)->factory()->getTTL() * 60;
        return response()->json(['code' => 0, 'message' => "success", "data" => [
            'accessToken' => $token,
            'refreshToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $expiresIn,
            'expires' => date("Y/m/d H:i:s", time() + $expiresIn),
            'name' => $name,
            'roles' => ['admin']
        ]])->setStatusCode(201);
    }
}
