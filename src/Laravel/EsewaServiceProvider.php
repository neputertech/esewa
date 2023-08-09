<?php

namespace Neputer\Laravel;

use Illuminate\Support\ServiceProvider;
use Neputer\Esewa;

final class EsewaServiceProvider extends ServiceProvider {

    public function boot() {
        $this->publishes([
            __DIR__.'/../config/esewa.php' => config_path('esewa.php')
        ], 'esewa-config');
    }

    public function register() {

        $this->mergeConfigFrom(__DIR__ . '/../config/esewa.php', 'esewa');

        $this->app->bind('esewa', function() {
            return new Esewa();
        });

        $this->app->singleton(Esewa::class, function () {
            return new Esewa();
        });

    }
}