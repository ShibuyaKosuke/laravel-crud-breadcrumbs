<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use ShibuyaKosuke\LaravelCrudBreadcrumbs\Breadcrumbs;

/**
 * Class BreadcrumbsServiceProvider
 * @package ShibuyaKosuke\LaravelCrudBreadcrumbs\Providers
 */
class BreadcrumbsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected $deferred = true;

    /**
     * boot
     * @see \Illuminate\View\Factory
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/breadcrumbs.php', 'breadcrumbs'
        );

        $this->loadViewsFrom(__DIR__ . '/../views', 'breadcrumbs');

        $this->registerClass();

        $this->publishes([
            __DIR__ . '/../config/breadcrumbs.php' => config_path('breadcrumbs.php'),
            __DIR__ . '/../views' => resource_path('views/vendor/breadcrumbs'),
        ], 'breadcrumbs');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/breadcrumbs.php', 'breadcrumbs');
        $this->app->singleton(Breadcrumbs::class, Breadcrumbs::class);
    }

    /**
     * @return array|string[]
     */
    public function provides()
    {
        return [Breadcrumbs::class];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function registerClass()
    {
        $this->app->make(Breadcrumbs::class);

        if (file_exists($file = $this->app['path.base'] . '/routes/breadcrumbs.php')) {
            require $file;
        }
    }
}
