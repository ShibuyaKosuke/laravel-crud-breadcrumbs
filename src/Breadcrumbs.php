<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

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
        $this->router = $app->router;
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
     * @return \Illuminate\Contracts\View\View
     * @throws DefinitionNotFoundException
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
    protected function buildCrumb(string $route): void
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
     * @throws DefinitionNotFoundException
     */
    protected function call($route): Crumb
    {
        /** @var Crumb $crumb */
        $crumb = $this->get($route);

        /** @var array $parameters */
        $parameters = ($cur_route = $this->router->current()) ? $cur_route->parameters : [];

        call_user_func_array(
            $this->getDefinition($route),
            Arr::prepend(array_values($parameters), $crumb)
        );

        return $crumb;
    }

    /**
     * @param $route
     * @return Closure
     * @throws DefinitionNotFoundException
     */
    protected function getDefinition(string $route)
    {
        if (!isset($this->callbacks, $this->callbacks[$route])) {
            throw new DefinitionNotFoundException();
        }
        return $this->callbacks[$route];
    }
}