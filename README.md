## About Site-Api

Laravel is a web application

Installation
------------

> This package requires PHP 7.4+ and Laravel 8.5

First, install laravel 8.5, and make sure that the database connection settings are correct.

```
composer require lsshu/site-api
```

Then run these commands to publish assets and config：

```
php artisan vendor:publish --provider="Lsshu\Site\Api\ServiceProvider --tag="config""
```

After run command you can find config file in `config/permisstion.php` `config/jwt.php` `config/site-api.php`, in this
file you can change the install directory,db connection or table names.

Add the following configuration to file `config/filesystems.php`
```
 'disks' => [
        ·
        ·
        ·
        'root' => [
            'driver' => 'local',
            'root' => '/'
        ]
    ]
```

At last run following command to finish install permission.

```
php artisan site-api:seed-permission
```

Open `http://localhost/site-api/` in browser,Test whether the installation is successful 🏅.

Configurations
------------
The file `config/site-api.php` contains an array of configurations, you can find the default configurations in there.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
