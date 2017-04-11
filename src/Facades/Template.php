<?php

namespace Pustato\LaravelBladeRenderFlow\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Template
 *
 * @package Pustato\LaravelBladeRenderFlow\Facades
 */
class Template extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'pustato::template';
    }
}