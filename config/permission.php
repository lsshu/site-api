<?php

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        /*
         * 当使用这个包中的 “HasRoles” 特性时，我们需要知道应该
         * 使用哪个 Eloquent 模型来获取您的权限。
         * 当然，它通常只是“权限（Permission）”模型，你也可以使用任何你喜欢的模型。
         *
         * 您使用的权限模型必须实现
         *  `Spatie\Permission\Contracts\Permission` 契约。
         */

        'menu' => Lsshu\Site\Api\Models\SystemMenu::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        /*
         * 当使用这个包中的 “HasRoles” 特性时，
         * 我们需要知道应该使用哪个 Eloquent 模型来检索你的角色。
         * 当然，它通常只是 “角色（Role）” 模型，你也可以使用任何你喜欢的模型。
         *
         * 您使用的权限模型必须实现
         * `Spatie\Permission\Contracts\Role` 契约。
         */

        'role' => Lsshu\Site\Api\Models\SystemRole::class,
        'user' => Lsshu\Site\Api\Models\SystemUser::class,
        'department' => Lsshu\Site\Api\Models\SystemDepartment::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */
        /*
         * 当使用这个包中的 “HasRoles” 特性时，
         * 我们需要知道哪个表应该用来检索你的“角色”。
         * 我们选择了一个基本的默认值，但您可以轻松将其更改为您喜欢的。
         */

        'roles' => 'site_api_system_roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */
        /*
         * 当使用这个包中的 “HasRoles” 特性时，
         * 我们需要知道哪个表应该用来检索你的权限。
         * 我们选择了一个基本的默认值，但您可以轻松将其更改为您喜欢的任何表。
         */

        'menus' => 'site_api_menus',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */
        /*
         *
         * 当使用这个包中的 “HasRoles” 特征时，
         * 我们需要知道应该使用哪个表来检索你的“模型权限”。
         * 我们选择了一个基本的默认值，但您可以轻松将其更改为您喜欢的任何表。
         *
         */

        'model_has_permissions' => 'site_api_system_model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        /*
         * 当使用这个包中的 “HasRoles” 特性时，
         * 我们需要知道哪个表应该用来检索你的“模型角色”。
         * 我们选择了一个基本的默认值，但您可以轻松将其更改为您喜欢的任何表。
         */

        'model_has_roles' => 'site_api_system_model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */
        /*
         * 当使用这个包中的 “HasRoles” 特性时，
         * 我们需要知道应该使用哪个表来检索您的“角色权限”。
         * 我们选择了一个基本的默认值，但您可以轻松将其更改为您喜欢的任何表。
         */

        'role_has_permissions' => 'site_api_system_role_has_permissions',

        'users' => 'site_api_system_users',

        'departments' => 'site_api_system_departments',

    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'role_pivot_key' => null, //default 'role_id',
        'permission_pivot_key' => null, //default 'permission_id',

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the teams feature and your related model's
         * foreign key is other than `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the method for checking permissions will be registered on the gate.
     * Set this to false, if you want to implement custom logic for checking permissions.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true the package implements teams using the 'team_foreign_key'. If you want
     * the migrations to register the 'team_foreign_key', you must set this to true
     * before doing the migration. If you already did the migration then you must make a new
     * migration to also add 'team_foreign_key' to 'roles', 'model_has_roles', and
     * 'model_has_permissions'(view the latest version of package's migration file)
     */

    'teams' => true,

    /*
     * 默认情况下，所有权限将被缓存24小时，
     * 除非更新许可或者更新角色来立即刷新缓存。
     */
    // 'cache_expiration_time' => 60 * 24,

    /*
     * When set to true, the required permission names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    /*
     * 设置为 true 时，所需的权限/角色名称（ permission/role）将添加到异常消息中。
     * 在某些情况下，这可能被认为是信息泄漏，
     * 所以为了获得最佳安全性，默认设置为 false。
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     */

    'enable_wildcard_permission' => false,

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],
];
