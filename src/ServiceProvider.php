<?php

namespace Haruncpi\LaravelLogReader;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/laravel-log-reader.php';
    const ROUTE_PATH = __DIR__ . '/../routes';
    const VIEW_PATH = __DIR__ . '/../views';
    const ASSET_PATH = __DIR__ . '/../assets';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('laravel-log-reader.php'),
        ], 'config');

        $this->loadRoutesFrom(self::ROUTE_PATH . '/web.php');
        $this->loadViewsFrom(self::VIEW_PATH, 'LaravelLogReader');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-log-reader'
        );
    }
}
