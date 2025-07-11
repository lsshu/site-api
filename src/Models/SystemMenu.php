<?php

namespace Lsshu\Site\Api\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemMenu extends SpatiePermission
{
    use SoftDeletes;

    protected $fillable = ['parentId', 'menuType', 'name', 'path', 'component', 'icon', 'title', 'rank','redirect','extraIcon','enterTransition','leaveTransition','activePath','auths','frameSrc','frameLoading','keepAlive','hiddenTag','fixedTag','showLink','showParent'];
//    protected $fillable = ['parent_id', 'name', 'path', 'title', 'icon', 'is_menu', 'is_action', 'guard_name'];

    // 模型文件
    public function children()
    {
        return $this->hasMany(get_class($this), 'parent_id', 'id');
    }

    public function childrenMenus()
    {
        return $this->children()->where('is_menu', true)->with('childrenMenus');
    }

    public function getTable()
    {
        return config('permission.table_names.menus', parent::getTable());
    }
}
