<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{
    protected string $guard_name = "site-api";

    /**
     * 登录后台 获取 token
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $credentials = [];
        $credentials['username'] = $request->username;
        $credentials['password'] = $request->password;
        $credentials['guard_name'] = $this->guard_name;
        if (!$token = Auth::guard($this->guard_name)->attempt($credentials)) {
            return $this->errorResponse(401, '用户名或密码错误', 1001);
        }
        return $this->respondWithToken($token, $credentials['username']);
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
            return $this->errorResponse(401, 'token 已过期', 1003);
        }
        // 删除成功，返回 204， 没有其他内容返回
        return response()->noContent();
    }

    /***
     * 检查登录状态
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function check()
    {
        try {
            $is_login = Auth::guard($this->guard_name)->check();
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'token 已过期', 1004);
        }
        // 删除成功，返回 204， 没有其他内容返回
        return response()->json([
            'success' => true,
            'code' => 0,
            'message' => "success",
            "data" => [
                "login" => $is_login
            ]])->setStatusCode(200);
    }

    /***
     * 用户信息
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function user()
    {
        try {
            $user = Auth::guard($this->guard_name)->user();
        } catch (\Exception $e) {
            return $this->errorResponse(401, 'token 已过期', 1005);
        }
        $user = $user->toArray();
        unset($user['password']);
        return response()->json([
            'success' => true,
            'code' => 0,
            'message' => "success",
            "data" => [
                "user" => $user
            ]])->setStatusCode(200);
    }

    /**
     * 返回 token
     *
     * @param $token
     * @param $name
     * @return mixed
     */
    public function respondWithToken($token, $name = null)
    {
        $expiresIn = Auth::guard($this->guard_name)->factory()->getTTL() * 60;
        $user = Auth::guard($this->guard_name)->user();
        return response()->json([
            'success' => true,
            'code' => 0,
            'message' => "success",
            "data" => [
                'accessToken' => $token,
                'refreshToken' => $token,
                'tokenType' => 'Bearer',
                'expiresIn' => $expiresIn,
                'expires' => date("Y/m/d H:i:s", time() + $expiresIn),
                'username' => $user->username,
                'roles' => ['admin']
            ]])->setStatusCode(201);
    }
}
