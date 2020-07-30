<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs;

use ShibuyaKosuke\LaravelCrudBreadcrumbs\Facades\Breadcrumbs;

/**
 * Class Crumb
 * @package ShibuyaKosuke\LaravelCrudBreadcrumbs
 *
 * @property string route
 * @property string title
 * @property string url
 * @property Crumb parent
 */
class Crumb
{
    /**
     * @var string route
     */
    private $route;

    /**
     * @var string title
     */
    private $title;

    /**
     * @var string url
     */
    private $url;

    /**
     * @var Crumb|null
     */
    private $parent;

    public function __construct(string $route)
    {
        $this->route = $route;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->__get($name);
    }

    /**
     * Set properties
     * @param string $title
     * @param string $url
     */
    public function add(string $title, string $url)
    {
        $this->title = $title;
        $this->url = $url;
    }

    /**
     * Set parent crumb
     * @param string|null $route
     */
    public function parent(string $route = null)
    {
        if (is_null($route)) {
            return;
        }
        $this->parent = Breadcrumbs::get($route);
    }
}