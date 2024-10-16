<?php

namespace Lsshu\Site\Api;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Lsshu\Site\Api\Console\Commands\SeedPermission;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'site-api');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/site-api'),
        ], 'views');
        $this->publishes([
            __DIR__ . '/../config/site-api.php' => config_path('site-api.php'),
            __DIR__ . '/../config/jwt.php' => config_path('jwt.php'),
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/site-api'),
        ], 'public');
        $this->publishes([
            __DIR__ . '/../database/migrations/2000_00_00_000000_create_permission_tables.php' => $this->getMigrationFileName('create_permission_tables.php'),
            __DIR__ . '/../database/migrations/2000_00_00_000001_add_teams_fields.php' => $this->getMigrationFileName('add_teams_fields.php'),
            __DIR__ . '/../database/migrations/2000_00_00_000002_create_root_or_team_tables.php' => $this->getMigrationFileName('create_root_or_team_tables.php'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedPermission::class
            ]);
        }
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/site-api.php', 'site-api');
        $this->mergeConfigFrom(__DIR__ . '/../config/jwt.php', 'jwt');
        $this->mergeConfigFrom(__DIR__ . '/../config/permission.php', 'permission');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path . '*_' . $migrationFileName);
            })->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")->first();
    }
}
