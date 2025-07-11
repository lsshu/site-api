<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class CreateTenantOrTenantPackageTables extends Migration
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

        Schema::create($tableNames['tenant_packages'], function (Blueprint $table) use ($columnNames, $tableNames) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 125)->comment("套餐名称");
            $table->string('remark', 191)->nullable()->comment("备注");
            $table->integer('status')->default('1')->comment("状态");
//            $table->string('guard_name', 80);
//            $table->foreign('guard_name')->references('guard_name')->on($tableNames['guards'])->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['name']);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create($tableNames['tenants'], function (Blueprint $table) use ($columnNames, $tableNames) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 125)->comment("租户名");
            $table->bigInteger('packageId')->comment("租户套餐");
            $table->string('contactName', 100)->comment("联系人");
            $table->string('contactMobile', 44)->comment("联系电话");
            $table->integer('accountCount')->default(0)->comment("账号额度");
            $table->bigInteger('expireTime')->default(0)->comment("过期时间");
            $table->string('website', 191)->comment("绑定域名");
            $table->string('remark', 191)->nullable()->comment("备注");
            $table->integer('status')->default('1')->comment("状态");
//            $table->string('guard_name', 80);
//            $table->foreign('guard_name')->references('guard_name')->on($tableNames['guards'])->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['name']);
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
        Schema::drop($tableNames['tenant_packages']);
        Schema::drop($tableNames['tenants']);
    }
}
