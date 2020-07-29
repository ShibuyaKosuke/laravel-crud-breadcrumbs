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

        $this->registerClass();
    }

    public function register()
    {
        $this->app->singleton(Breadcrumbs::class, Breadcrumbs::class);
    }

    public function provides()
    {
        return [Breadcrumbs::class];
    }

    public function registerClass()
    {
        $this->app->make(Breadcrumbs::class);

        if (file_exists($file = $this->app['path.base'] . '/routes/breadcrumbs.php')) {
            require $file;
        }
    }
}
