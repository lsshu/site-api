<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class CreateUserOrDepartmentTables extends Migration
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

        Schema::create($tableNames['departments'], function (Blueprint $table) use ($columnNames) {
            $table->bigIncrements('id'); // role id
            $table->unsignedBigInteger('parentId')->nullable()->comment("上级部门"); // parent id
            $table->string('name', 125)->comment("部门名称");       // For MySQL 8.0 use string('name', 125);
            $table->string('principal', 125)->nullable()->comment("部门负责人");
            $table->string('phone', 44)->nullable()->comment("手机号");
            $table->string('email', 100)->nullable()->comment("邮箱");
            $table->string('remark', 191)->nullable()->comment("备注");
            $table->integer('sort')->comment("排序");
            $table->integer('status')->default('1')->comment("状态");
            $table->foreign('guard_name',80)->references('guard_name')->on('guard')->onUpdate('cascade')->onDelete('cascade');
//            $table->string('guard_name', 80); // For MySQL 8.0 use string('guard_name', 125);
            $table->unique(['name', 'guard_name', 'parentId']);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create($tableNames['users'], function (Blueprint $table) use ($columnNames) {
            $table->bigIncrements('id'); // root id
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
            $table->index($columnNames['team_foreign_key']);
            $table->string('username', 125)->comment("用户名称");       // For MySQL 8.0 use string('name', 125);
            $table->string('nickname', 125)->nullable()->comment("用户昵称");       // For MySQL 8.0 use string('name', 125);
            $table->string('avatar', 125)->nullable()->comment("用户头像");       // For MySQL 8.0 use string('name', 125);
            $table->string('phone', 44)->nullable()->comment("手机号");
            $table->string('email', 100)->nullable()->comment("邮箱");
            $table->integer('sex')->default('1')->comment("用户性别");
            $table->string('remark', 191)->nullable()->comment("备注");
            $table->unsignedBigInteger('deptId')->nullable()->comment("归属部门"); // parent id
            $table->string('password', 125);
            $table->integer('status')->default('1')->comment("状态");
//            $table->string('guard_name', 125); // For MySQL 8.0 use string('guard_name', 125);
            $table->foreign('guard_name',80)->references('guard_name')->on('guard')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['username', 'guard_name']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');
        Schema::drop($tableNames['departments']);
        Schema::drop($tableNames['users']);
    }
}
