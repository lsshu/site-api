<?php

namespace Lsshu\Site\Api\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lsshu\Site\Api\Models\SystemMenu;
use Lsshu\Site\Api\Models\SystemUser;

class SeedPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site-api:seed-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '填充权限';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 获取 zh-CN 语言包中的权限下所有文件
        $dirname = 'vendor' . DIRECTORY_SEPARATOR . 'lsshu' . DIRECTORY_SEPARATOR . 'site-api' . DIRECTORY_SEPARATOR . 'permission';
        $files = Storage::disk('root')->files($dirname);
        try {
            is_null(!$files);
        } catch (\Exception $e) {
            report($e);
            return false;
        }

        DB::transaction(function () use ($files, $dirname) {
            foreach ($files as $file) {
                // 获取守卫名称
//                $guardName = basename($file, '.php');
                $array = include($file);
                $values = [];

                foreach ($array as $arr) {
                    $arr['name'] = $arr['name'] ?? $arr['title'];
                    $values[$arr['name']] = $arr;
                    if (isset($arr['action']) && $arr['action']) {
                        $values = array_merge($values, $this->action($arr['action'], $arr['path'], $arr['name'], $arr['title'], !(isset($arr['children']) && $arr['children'])));
                        unset($values[$arr['name']]['action']);
                    }
                    if (isset($arr['children']) && $arr['children']) {
                        $values = array_merge($values, $this->children($arr['children'], $arr['path'], $arr['name'], $arr['title']));
                        unset($values[$arr['name']]['children']);
                    }
                }
//                dd($values);
                // 获取数据库中的权限
                $permissions = SystemMenu::select('path')->get()->pluck('path');
//                $permissions = SystemMenu::select('path')->where('guard_name', $guardName)->get()->pluck('path');
                // 筛选出不同的权限
                //$diff = collect(array_keys($values))->diff($permissions);
                $diff = collect(array_keys($values));
                foreach ($diff as $item) {
                    $_item = $values[$item];
//                    $_item['guard_name'] = isset($_item['guard_name']) ? $_item['guard_name'] : $guardName;
                    if (isset($_item['parent_name']) && $_item['parent_name']) {
                        $p = SystemMenu::where(['name' => $_item['parent_name']])->first();
                        if ($p) {
                            $_item['parentId'] = $p->id;
                        }
                    }
//                    SystemMenu::updateOrCreate(['path' => $_item['path'], 'guard_name' => $_item['guard_name']], $_item);
                    SystemMenu::updateOrCreate(['name' => $_item['name']], $_item);
                }
                // 反向删除
                $diff2 = collect($permissions)->diff(array_keys($values));
                foreach ($diff2 as $item) {
                    SystemMenu::where(['name' => $item])->delete();
//                    SystemMenu::where(['path' => $item, 'guard_name' => $guardName])->delete();
                }
            }
        });
        (new SystemUser())->initRootUser();
        $this->info('权限已更新');
        return Command::SUCCESS;
    }

    /***
     *
     * @param $children
     * @param $parent_name
     * @return array
     */
    protected function children($children, $parent_path = null, $parent_name = null, $parent_title = null)
    {
        $values = [];
        foreach ($children as $child) {
            $child['path'] = $parent_path . '/' . $child['path'];
            $child['name'] = $child['name'] ?? $child['title'];
            $values[$child['name']] = $child;

            $values = array_merge($values, $this->action(isset($child['action']) && is_array($child['action']) ? $child['action'] : [], $child['path'], $child['name'], $child['title']));
            unset($values[$child['path']]['action']);


            if (isset($child['children']) && $child['children']) {
                $values = array_merge($values, $this->children($child['children'], $child['path'], $child['name'], $child['title']));
                unset($values[$child['path']]['children']);
            }

        }
        return $values;
    }

    /***
     * @param $children
     * @param $parent_name
     * @param $parent_title
     * @param $is_default_action
     * @return array
     */
    protected function action($children, $parent_path = null, $parent_name = null, $parent_title = null, $is_default_action = true)
    {
        $values = [];
        $action = [
            ['path' => "index", 'name' => "Index", 'title' => "列表"],
            ['path' => "show", 'name' => "Show", 'title' => "查看"],
            ['path' => "store", 'name' => "Store", 'title' => "添加"],
            ['path' => "update", 'name' => "Update", 'title' => "修改"],
            ['path' => "destroy", 'name' => "Destroy", 'title' => "删除"],
            ['path' => "export", 'name' => "Export", 'title' => "导出"]
        ];
        $children_action = $is_default_action ? array_merge($action, $children) : $children;
        foreach ($children_action as $child) {
            $temp = [];
            $temp['path'] = $parent_path . '/' . $child['path'];
            $temp['name'] = $parent_name . '.' . $child['name'];
            $temp['title'] = $parent_title . '.' . $child['name'];
            $temp['parent_name'] = $parent_name;
            $values[$temp['name']] = $temp;
        }
        return $values;
    }
}
