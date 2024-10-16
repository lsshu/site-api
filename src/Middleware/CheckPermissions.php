<?php

namespace Lsshu\Site\Api\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Guard;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 超级管理员默认通过所有权限
        if (Auth::guard("site-api")->user()->isRoot()) {
            return $next($request);
        }

        // 获取当前路由名称
        $currentRouteName = Route::currentRouteName();

        // 获取当前守卫名称
        $guardName = Guard::getDefaultName(self::class);
        // 引入当前守卫的权限文件
        $routes = include(__DIR__.'/../../routes/exclude/' . $guardName . '.php');

        // 替换设置了关联关系的权限
        if (is_array($routes) && key_exists($currentRouteName, $routes)) {
            $currentRouteName = $routes[$currentRouteName];
        }

        // 当路由不为 null 时，验证权限
        if (!is_null($currentRouteName)) {
            Gate::authorize($currentRouteName);
        }

        return $next($request);
    }
}
