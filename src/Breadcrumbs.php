<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use ShibuyaKosuke\LaravelCrudBreadcrumbs\Exceptions\DefinitionAlreadyExistsException;
use ShibuyaKosuke\LaravelCrudBreadcrumbs\Exceptions\DefinitionNotFoundException;

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
     * @param string $name
     * @param Closure $definition
     * @return void
     * @throws DefinitionAlreadyExistsException
     */
    public function for(string $name, Closure $definition): void
    {
        if (isset($this->callbacks[$name])) {
            throw new DefinitionAlreadyExistsException();
        }
        $this->callbacks[$name] = $definition;

        $this->add($name);
    }

    /**
     * Add to breadcrumbs
     * @param string $route
     */
    public function add(string $route): void
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
     * @return Crumb|null
     */
    public function get($route)
    {
        return $this->breadcrmbs->get($route);
    }

    /**
     */
    public function render()
    {
        $this->buildCrumb(\Route::currentRouteName());
        if ($breadcrumbs = $this->current) {
            return $this->view->make(
                $this->config->get('breadcrumbs.view'),
                compact('breadcrumbs')
            );
        }
    }

    /**
     * @param $route
     * @throws DefinitionNotFoundException
     */
    protected function buildCrumb($route): void
    {
        $crumb = $this->call($route);
        $this->current->prepend($crumb);
        if ($crumb->parent) {
            $this->buildCrumb($crumb->parent->route);
        }
    }

    /**
     * @param $route
     * @return Crumb
     */
    protected function call($route): Crumb
    {
        $params = [];
        $crumb = $this->get($route);
        if (!isset($this->callbacks[$route])) {
            throw new DefinitionNotFoundException();
        }
        $callback = $this->callbacks[$route];
        $callback($crumb, $params);
        return $crumb;
    }
}