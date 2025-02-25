<?php

namespace Itmm\Sso;

use Illuminate\Support\ServiceProvider;

class SsoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/App/Http/Middleware' => base_path('app/Http/Middleware'),
            __DIR__ . '/App/Http/Controllers' => base_path('app/Http/Controllers'),
            __DIR__ . '/config' => base_path('config'),
            __DIR__ . '/resources' => base_path('resources'),
        ], 'sso-components');
        
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
