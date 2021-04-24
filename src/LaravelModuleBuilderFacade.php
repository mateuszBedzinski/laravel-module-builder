<?php

namespace Mbedzinski\LaravelModuleBuilder;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mbedzinski\LaravelModuleBuilder\LaravelModuleBuilder
 */
class LaravelModuleBuilderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel_module_builder';
    }
}
