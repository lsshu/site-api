<?php

use Illuminate\Support\Facades\Route;
use Lsshu\Site\Api\Controllers\SiteApi\AuthorizationsController;
use Lsshu\Site\Api\Controllers\SiteApi\RootsController;
use Lsshu\Site\Api\Controllers\SiteApi\RolesController;
use Lsshu\Site\Api\Controllers\SiteApi\PermissionsController;
use Lsshu\Site\Api\Controllers\SiteApi\TeamsController;

Route::group([
    'prefix' => 'site-api'
], function () {
    Route::get('test', function () {
        return ["status" => "success"];
    })->name('test'); // 测试是否安装成功
    // 登录接口
    Route::group([
    ], function () {
        // 获取 token
        Route::get('login', [AuthorizationsController::class, "login"])->name('login'); // 默认登录
        Route::post('login', [AuthorizationsController::class, "store"])->name('login');
        // 刷新 token
        Route::put('refresh', [AuthorizationsController::class, "refresh"])->name('login.refresh');
        // 删除 token
        Route::delete('logout', [AuthorizationsController::class, "destroy"])->name('login.logout');
    });

    Route::group([
        'middleware' => ['auth:site-api'],// 登录就可以
    ], function () {
        /*菜单*/
        Route::get("menus", [PermissionsController::class, "menus"])->name('site-api.menus');
    });

    // 需要 token 验证的接口
    Route::group([
        'middleware' => ['auth:site-api', 'check.permissions'],// 此处认证的是 api 守卫
    ], function () {
        /*管理员*/
        Route::resource('roots', RootsController::class)->names("site-api.roots");
        /*角色*/
        Route::resource('roles', RolesController::class)->names("site-api.roles");
        /*权限*/
        Route::resource('permissions', PermissionsController::class)->names("site-api.permissions");
        /*团队*/
        Route::resource('teams', TeamsController::class)->names("site-api.teams");
    });
});
