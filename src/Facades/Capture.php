<?php

namespace Pustato\LaravelBladeRenderFlow\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Capture
 *
 * @package Pustato\LaravelBladeRenderFlow\Facades
 */
class Capture extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'pustato::capture';
    }
}
