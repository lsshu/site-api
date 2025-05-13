<?php

use Illuminate\Support\Facades\Route;
use Lsshu\Site\Api\Controllers\SiteApi\AuthorizationsController;
use Lsshu\Site\Api\Controllers\SiteApi\MiniProgramController;
use Lsshu\Site\Api\Controllers\SiteApi\UserController;
use Lsshu\Site\Api\Controllers\SiteApi\RoleController;
use Lsshu\Site\Api\Controllers\SiteApi\MenuController;
use Lsshu\Site\Api\Controllers\SiteApi\DepartmentController;
use Lsshu\Site\Api\Controllers\SiteApi\TenantsController;
use Lsshu\Site\Api\Controllers\SiteApi\TenantPackagesController;

Route::group([
    'prefix' => 'site-api'
], function () {
    Route::get('test', function () {
        return ["status" => "success"];
    })->name('test'); // 测试是否安装成功
    // 登录接口
    Route::group([], function () {
        // 默认登录 获取 token
        Route::match(['post', 'put'], 'login', [AuthorizationsController::class, "login"])->name('site-api.login'); // 默认登录
    });
    Route::group([
        'middleware' => ['auth:site-api'],// 登录就可以
    ], function () {
        // 登录信息
        Route::match(['get', 'post', 'put'], 'user', [AuthorizationsController::class, "user"])->name('site-api.login.user');
        // 刷新 token
        Route::match(['get', 'post'], 'refresh-token', [AuthorizationsController::class, "refresh"])->name('site-api.login.refresh');
        // 检查 token
        Route::match(['get', 'post'], 'check', [AuthorizationsController::class, "check"])->name('site-api.login.check');
        // 删除 token
        Route::match(['get', 'post', 'delete'], 'logout', [AuthorizationsController::class, "logout"])->name('site-api.login.logout');
    });

    Route::group([
        'middleware' => ['auth:site-api'],// 登录就可以
    ], function () {
        /*菜单*/
        Route::match(['get', 'post'], "get-async-routes", [MenuController::class, "routes"])->name('site-api.routes');
    });

    // 需要 token 验证的接口
    Route::group([
        'middleware' => ['auth:site-api', 'check.permissions'],// 此处认证的是 api 守卫
    ], function () {
        /*管理员*/
        Route::resource('users', UserController::class)->names("site-api.users");
        /*角色*/
        Route::resource('roles', RoleController::class)->names("site-api.roles");
        /*权限*/
        Route::resource('permissions', MenuController::class)->names("site-api.permissions");
        /*团队*/
        Route::resource('departments', DepartmentController::class)->names("site-api.departments");

        /*商户*/
        Route::resource('tenants', TenantsController::class)->names("site-api.tenants");
        /*商户套餐*/
        Route::resource('tenant/packages', TenantPackagesController::class)->names("site-api.tenants");
    });

    // 微信小程序
    Route::group([
        'prefix' => 'program'
    ], function () {
        // 微信小程序登录 获取openid
        Route::post('user_login', [MiniProgramController::class, "user_login"])->name('site-api.program.user_login');
        // 解密微信用户手机号
        Route::post('user_phone_number', [MiniProgramController::class, "user_phone_number"])->name('site-api.program.user_phone_number');
        // 获取微信用户手机号
        Route::post('get_user_phone_number', [MiniProgramController::class, "get_user_phone_number"])->name('site-api.program.get_user_phone_number');
        // 解密微信用户信息
        Route::post('user_info', [MiniProgramController::class, "user_info"])->name('site-api.program.user_info');
    });
});
