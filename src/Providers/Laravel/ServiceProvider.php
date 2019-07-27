<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Providers\Laravel;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Rugaard\Pollen\Pollen;

/**
 * Class ServiceProvider
 *
 * @package Rugaard\DMI\Providers\Laravel
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->singleton('rugaard.pollen', function ($app) {
            return new Pollen;
        });

        $this->app->bind(Pollen::class, function ($app) {
            return $app['rugaard.pollen'];
        });
    }
    /**
     * Get the services provided by this provider.
     *
     * @return array
     */
    public function provides() : array
    {
        return ['rugaard.pollen'];
    }
}