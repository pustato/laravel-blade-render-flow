<?php

namespace Pustato\LaravelBladeRenderFlow;


use Illuminate\Support\Facades\Blade;
use Pustato\LaravelBladeRenderFlow\Exceptions\InvalidDirectiveUsageException;
use Pustato\LaravelBladeRenderFlow\Facades\Capture;
use Pustato\LaravelBladeRenderFlow\Facades\Template;

/**
 * Class ServiceProvider
 *
 * @package Pustato\LaravelBladeRenderFlow
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Register services
     */
    public function register()
    {
        $this->registerCapture();
        $this->registerTemplate();
    }

    /**
     * Register Template extension
     */
    private function registerTemplate()
    {
        $this->app->singleton('pustato::template', function() {
            return new \Pustato\LaravelBladeRenderFlow\Extensions\Template(function ($str) {
                return Blade::compileString($str);
            });
        });

        Blade::extend(Template::class.'::extractTemplates');
        Blade::directive('render', Template::class.'::renderTemplate');
    }

    /**
     * Register Capture extension
     */
    private function registerCapture()
    {
        $this->app->singleton('pustato::capture', \Pustato\LaravelBladeRenderFlow\Extensions\Capture::class);

        $facadeClass = Capture::class;
        Blade::extend(function($view) {
            if (substr_count($view, '@capture') !== (substr_count($view, '@endcapture') + substr_count($view, '@flushcapture'))) {
                throw new InvalidDirectiveUsageException("Count of @capture and @endcapture/@flushcapture usages must match in each view.");
            }

            return $view;
        });

        Blade::directive('capture', function($blockName) use ($facadeClass) {
            $blockName = trim($blockName, '\'"');
            return "<?php ob_start(${facadeClass}::getOBCallback('${blockName}')); ?>";
        });
        Blade::directive('endcapture', function() {
            return "<?php ob_end_clean(); ?>";
        });
        Blade::directive('flushcapture', function() {
            return "<?php ob_end_flush(); ?>";
        });
        Blade::directive('flush', function($blockName) use ($facadeClass) {
            $blockName = trim($blockName, '\'"');
            return "<?php echo ${facadeClass}::getBlock('${blockName}'); ?>";
        });
        Blade::directive('clearcapture', function($blockName = null) use ($facadeClass) {
            if ($blockName) {
                $blockName = trim($blockName, "'");
                return "<?php echo ${facadeClass}::clearBlock('${blockName}'); ?>";
            }
            return "<?php echo ${facadeClass}::clear(); ?>";
        });
    }
}
