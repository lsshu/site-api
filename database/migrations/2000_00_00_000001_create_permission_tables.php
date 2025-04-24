<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id'); // permission id
            $table->unsignedBigInteger('parentId')->nullable()->comment("上级菜单"); // parent id
            $table->integer('menuType')->default(0)->comment("菜单类型");
            $table->string('name', 125)->comment("路由名称");       // For MySQL 8.0 use string('name', 125);
            $table->string('path', 125)->comment("路由路径");       // For MySQL 8.0 use string('path', 125);
            $table->string('component', 125)->nullable()->comment("组件路径");       // For MySQL 8.0 use string('path', 125);
            $table->string('icon', 100)->nullable()->comment("菜单图标");       // For MySQL 8.0 use string('icon', 32);
            $table->string('title', 125)->comment("菜单名称");       // For MySQL 8.0 use string('title', 125);
            $table->integer('rank')->default(0)->comment("菜单排序");
            $table->string('redirect', 125)->nullable()->comment("路由重定向");
            $table->string('extraIcon', 125)->nullable()->comment("右侧图标");
            $table->string('enterTransition', 125)->nullable()->comment("进场动画");
            $table->string('leaveTransition', 125)->nullable()->comment("离场动画");
            $table->string('activePath', 125)->nullable()->comment("菜单激活");
            $table->string('auths', 125)->nullable()->comment("权限标识");
            $table->string('frameSrc', 125)->nullable()->comment("链接地址");
            $table->boolean('frameLoading')->default(true)->comment("加载动画");
            $table->boolean('keepAlive')->default(false)->comment("缓存页面");
            $table->boolean('hiddenTag')->default(false)->comment("标签页");
            $table->boolean('fixedTag')->default(false)->comment("固定标签页");
            $table->boolean('showLink')->default(true)->comment("菜单");
            $table->boolean('showParent')->default(false)->comment("父级菜单");
//            $table->boolean('is_menu')->default(true);
//            $table->boolean('is_action')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'parentId']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames, $tableNames) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id'); // role id
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name', 125)->comment("角色名称");       // For MySQL 8.0 use string('name', 125);
            $table->string('code', 125)->nullable()->comment("角色标识");       // For MySQL 8.0 use string('name', 125);
            $table->integer('status')->default('1')->comment("状态");
            $table->string('remark', 191)->nullable()->comment("备注");
//            $table->string('guard_name', 80);
//            $table->foreign('guard_name')->references('guard_name')->on($tableNames['guards'])->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name']);
            } else {
                $table->unique(['name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);

            $table->string('model_type', 191);
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->string('model_type', 191);
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
