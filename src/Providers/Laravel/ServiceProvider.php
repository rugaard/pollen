<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Providers\Laravel;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Rugaard\Pollen\Pollen;

/**
 * Class ServiceProvider.
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
        $this->app->singleton(abstract: 'rugaard.pollen', concrete: fn () => new Pollen);
        $this->app->alias(abstract: 'rugaard.pollen', alias: Pollen::class);
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
