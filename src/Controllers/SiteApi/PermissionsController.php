<?php

namespace Lsshu\Site\Api\Controllers\SiteApi;

use Illuminate\Http\Request;
use Lsshu\Site\Api\Models\Permission;

class PermissionsController extends Controller
{
    protected string $model = Permission::class;

    /***
     * 获取所有菜单
     * @return \Illuminate\Http\JsonResponse
     */
    public function menus()
    {
        $data = $this->model::whereNull("parent_id")->where('is_menu', true)->with('childrenMenus')->get();
        $data = $this->handleMenus($data);
        return $this->response($data);
    }

    protected function handleMenus($data)
    {
        $menus = [];
        foreach ($data as $key => $datum) {
            $menus[$key] = [
                "path" => "/" . str_replace('.', '/', $datum->name),
                "meta" => [
                    "icon" => $datum->icon,
                    "title" => $datum->title,
                    "rank" => $datum->rank,
                ],
            ];
            if (!$datum->childrenMenus->isEmpty()) {
                $menus[$key]['children'] = $this->handleMenus($datum->childrenMenus);
            }
            if (isset($datum['children']) && !$datum['children']) {
                unset($menus[$key]['children']);
            }
        }
        return $menus;
    }
}
