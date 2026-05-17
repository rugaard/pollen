<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Providers\Laravel;

use Override;
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
    #[Override]
    public function register() : void
    {
        $this->app->singleton(abstract: 'rugaard.pollen', concrete: fn (): Pollen => new Pollen);
        $this->app->alias(abstract: 'rugaard.pollen', alias: Pollen::class);
    }

    /**
     * Get the services provided by this provider.
     *
     * @return array<int, string>
     */
    #[Override]
    public function provides() : array
    {
        return ['rugaard.pollen'];
    }
}
