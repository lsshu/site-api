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
                $guardName = basename($file, '.php');
                $array = include($file);
                $values = [];

                foreach ($array as $arr) {
                    $arr['is_menu'] = $arr['is_menu'] ?? true;
                    $arr['is_action'] = $arr['is_action'] ?? true;
                    $values[$arr['path']] = $arr;
                    if (isset($arr['action']) && $arr['action']) {
                        $values = array_merge($values, $this->action($arr['action'], $arr['path'], $arr['title'], !(isset($arr['children']) && $arr['children'])));
                        unset($values[$arr['path']]['action']);
                    }
                    if (isset($arr['children']) && $arr['children']) {
                        $values = array_merge($values, $this->children($arr['children'], $arr['path']));
                        unset($values[$arr['path']]['children']);
                    }
                }
                // 获取数据库中的权限
                $permissions = SystemMenu::select('path')->where('guard_name', $guardName)->get()->pluck('path');
                // 筛选出不同的权限
                //$diff = collect(array_keys($values))->diff($permissions);
                $diff = collect(array_keys($values));
                foreach ($diff as $item) {
                    $_item = $values[$item];
                    $_item['guard_name'] = isset($_item['guard_name']) ? $_item['guard_name'] : $guardName;
                    if (isset($_item['parent_name']) && $_item['parent_name']) {
                        $p = SystemMenu::where(['path' => $_item['parent_name'], 'guard_name' => $_item['guard_name']])->first();
                        if ($p) {
                            $_item['parent_id'] = $p->id;
                        }
                    }
                    SystemMenu::updateOrCreate(['path' => $_item['path'], 'guard_name' => $_item['guard_name']], $_item);
                }
                // 反向删除
                $diff2 = collect($permissions)->diff(array_keys($values));
                foreach ($diff2 as $item) {
                    SystemMenu::where(['path' => $item, 'guard_name' => $guardName])->delete();
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
    protected function children($children, $parent_name = null)
    {
        $values = [];
        foreach ($children as $child) {
            $child['is_menu'] = $child['is_menu'] ?? true;
            $child['is_action'] = $child['is_action'] ?? true;
            $child['path'] = $parent_name . '.' . $child['path'];
            $child['parent_name'] = $parent_name;
            $values[$child['path']] = $child;

            $values = array_merge($values, $this->action(isset($child['action']) && is_array($child['action']) ? $child['action'] : [], $child['path'], $child['title']));
            unset($values[$child['path']]['action']);


            if (isset($child['children']) && $child['children']) {
                $values = array_merge($values, $this->children($child['children'], $child['path']));
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
    protected function action($children, $parent_name = null, $parent_title = null, $is_default_action = true)
    {
        $values = [];
        $action = [
            ['path' => "index", 'title' => "列表"],
            ['path' => "show", 'title' => "查看"],
            ['path' => "store", 'title' => "添加"],
            ['path' => "update", 'title' => "修改"],
            ['path' => "destroy", 'title' => "删除"],
            ['path' => "export", 'title' => "导出"]
        ];
        $children_action = $is_default_action ? array_merge($action, $children) : $children;
        foreach ($children_action as $child) {
            $child['is_menu'] = $child['is_menu'] ?? false;
            $child['is_action'] = $child['is_action'] ?? true;
            $child['path'] = $parent_name . '.' . $child['path'];
            $child['title'] = $parent_title . ' ' . $child['title'];
            $child['parent_name'] = $parent_name;
            $values[$child['path']] = $child;
        }
        return $values;
    }
}
