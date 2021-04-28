<?php

namespace Kapitanluffy\AsyncRedis;

use Illuminate\Support\ServiceProvider;

class AsyncRedisServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['redis']->extend('async-redis', function () {
            return new AsyncRedisConnector();
        });
    }

    public function register()
    {
        $this->app->singleton('async-redis', function ($app) {
            $driver = $app->make('config')->get('database.redis.client', 'predis');

            $app['redis']->setDriver('async-redis');
            $redis = $app['redis']->connection();
            $app['redis']->setDriver($driver);

            return $redis;
        });
    }
}
