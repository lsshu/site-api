<?php

namespace Lsshu\Site\Api\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lsshu\Site\Api\Models\SystemMenu;

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
        $dirname = __DIR__ . "/../../../permission/";
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
                $guardName = basename($file, '.php');
                $array = include($dirname . $guardName . '.php');
                $values = [];

                foreach ($array as $arr) {
                    $arr['is_menu'] = $arr['is_menu'] ?? true;
                    $arr['is_action'] = $arr['is_action'] ?? true;
                    $values[$arr['name']] = $arr;
                    if (isset($arr['action']) && $arr['action']) {
                        $values = array_merge($values, $this->action($arr['action'], $arr['name'], $arr['title'], !(isset($arr['children']) && $arr['children'])));
                        unset($values[$arr['name']]['action']);
                    }
                    if (isset($arr['children']) && $arr['children']) {
                        $values = array_merge($values, $this->children($arr['children'], $arr['name']));
                        unset($values[$arr['name']]['children']);
                    }
                }
                // 获取数据库中的权限
                $permissions = SystemMenu::select('name')->where('guard_name', $guardName)->get()->pluck('name');
                // 筛选出不同的权限
                //$diff = collect(array_keys($values))->diff($permissions);
                $diff = collect(array_keys($values));
                foreach ($diff as $item) {
                    $_item = $values[$item];
                    $_item['guard_name'] = isset($_item['guard_name']) ? $_item['guard_name'] : $guardName;
                    if (isset($_item['parent_name']) && $_item['parent_name']) {
                        $p = SystemMenu::where(['name' => $_item['parent_name'], 'guard_name' => $_item['guard_name']])->first();
                        if ($p) {
                            $_item['parent_id'] = $p->id;
                        }
                    }
                    SystemMenu::updateOrCreate(['name' => $_item['name'], 'guard_name' => $_item['guard_name']], $_item);
                }
                // 反向删除
                $diff2 = collect($permissions)->diff(array_keys($values));
                foreach ($diff2 as $item) {
                    SystemMenu::where(['name' => $item, 'guard_name' => $guardName])->delete();
                }
            }
        });

        $this->info('权限已更新');
        return Command::SUCCESS;
    }

    /***
     *
     * @param $children
     * @param $parent_name
     * @return array
     */
    protected function children($children, $parent_name = null)
    {
        $values = [];
        foreach ($children as $child) {
            $child['is_menu'] = $child['is_menu'] ?? true;
            $child['is_action'] = $child['is_action'] ?? true;
            $child['name'] = $parent_name . '.' . $child['name'];
            $child['parent_name'] = $parent_name;
            $values[$child['name']] = $child;

            $values = array_merge($values, $this->action(isset($child['action']) && is_array($child['action']) ? $child['action'] : [], $child['name'], $child['title']));
            unset($values[$child['name']]['action']);


            if (isset($child['children']) && $child['children']) {
                $values = array_merge($values, $this->children($child['children'], $child['name']));
                unset($values[$child['name']]['children']);
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
    protected function action($children, $parent_name = null, $parent_title = null, $is_default_action = true)
    {
        $values = [];
        $action = [
            ['name' => "index", 'title' => "列表"],
            ['name' => "show", 'title' => "查看"],
            ['name' => "store", 'title' => "添加"],
            ['name' => "update", 'title' => "修改"],
            ['name' => "destroy", 'title' => "删除"],
            ['name' => "export", 'title' => "导出"]
        ];
        $children_action = $is_default_action ? array_merge($action, $children) : $children;
        foreach ($children_action as $child) {
            $child['is_menu'] = $child['is_menu'] ?? false;
            $child['is_action'] = $child['is_action'] ?? true;
            $child['name'] = $parent_name . '.' . $child['name'];
            $child['title'] = $parent_title . ' ' . $child['title'];
            $child['parent_name'] = $parent_name;
            $values[$child['name']] = $child;
        }
        return $values;
    }
}
