<?php

namespace ShibuyaKosuke\LaravelCrudBreadcrumbs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class BreadcrumbsFacade
 * @package ShibuyaKosuke\LaravelCrudBreadcrumbs\Facades
 */
class Breadcrumbs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ShibuyaKosuke\LaravelCrudBreadcrumbs\Breadcrumbs::class;
    }
}