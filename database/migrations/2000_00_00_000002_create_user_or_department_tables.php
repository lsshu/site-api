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
            $table->unsignedBigInteger('parent_id')->nullable(); // parent id
            $table->string('name', 125);       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name', 125); // For MySQL 8.0 use string('guard_name', 125);
            $table->unique(['name', 'guard_name', 'parent_id']);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create($tableNames['users'], function (Blueprint $table) use ($columnNames) {
            $table->bigIncrements('id'); // root id
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
            $table->index($columnNames['team_foreign_key'], 'roots_team_foreign_key_index');
            $table->string('username', 125);       // For MySQL 8.0 use string('name', 125);
            $table->string('nickname', 125)->nullable();       // For MySQL 8.0 use string('name', 125);
            $table->string('avatar', 125)->nullable();       // For MySQL 8.0 use string('name', 125);
            $table->string('password', 125);
            $table->string('guard_name', 125); // For MySQL 8.0 use string('guard_name', 125);
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
