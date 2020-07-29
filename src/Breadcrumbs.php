<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Class Breadcrumbs
 * @package ShibuyaKosuke\LaravelCrudBreadcrumbs
 */
class Breadcrumbs
{
    /**
     * The view factory.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * The config repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var Collection
     */
    protected $breadcrmbs;

    /**
     * @var array|Closure[]
     */
    protected $callbacks = [];

    /**
     * @var array
     */
    protected $current;

    /**
     * Create the instance of the Breadcrumbs.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->view = $app->view;
        $this->config = $app->config;
        $this->breadcrmbs = collect();
        $this->current = collect();
    }

    /**
     * Register a breadcrumb definition by passing it off to the registrar.
     *
     * @param string $route
     * @param Closure $definition
     * @return void
     */
    public function for(string $route, Closure $definition)
    {
        if (isset($this->callbacks[$route])) {
            return;
        }
        $this->callbacks[$route] = $definition;

        $this->add($route);
    }

    /**
     * Add to breadcrumbs
     * @param string $route
     */
    public function add(string $route)
    {
        $trail = new Crumb($route);
        $this->breadcrmbs->put($route, $trail);
    }

    /**
     * @param string $route
     * @return bool
     */
    public function has(string $route): bool
    {
        return $this->breadcrmbs->has($route);
    }

    /**
     * @param $route
     * @return Crumb
     */
    public function get($route): Crumb
    {
        return $this->breadcrmbs->get($route);
    }

    /**
     * @return Crumb[]
     */
    public function render()
    {
        $route = \Route::currentRouteName();
        $this->buildCrumb($route);
        return $this->current;
    }

    private function buildCrumb($route)
    {
        $params = [];
        $crumb = $this->get($route);
        $this->callbacks[$route]($crumb, $params);
        $this->current->prepend($crumb);
        if ($crumb->parent) {
            $this->buildCrumb($crumb->parent->route);
        }
    }
}