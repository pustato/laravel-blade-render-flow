<?php


namespace Pustato\LaravelBladeRenderFlow\Extensions;

use Pustato\LaravelBladeRenderFlow\Exceptions\InvalidDirectiveUsageException;
use Pustato\LaravelBladeRenderFlow\Exceptions\TemplateNotFoundException;

/**
 * Class Template
 *
 * @package Pustato\LaravelBladeRenderFlow\Extensions
 */
class Template
{
    /** @var array  */
    private $templates = [];

    /** @var \Closure  */
    private $compileString;

    /**
     * Template constructor.
     *
     * @param \Closure $compileString
     */
    public function __construct(\Closure $compileString)
    {
        $this->compileString = $compileString;
    }

    /**
     * Extract template blocks from view
     *
     * @param string $view
     * @return string
     */
    public function extractTemplates($view)
    {
        if (strpos($view, '@template') === false) {
            return $view;
        }

        $view = preg_replace_callback('/(?<!@)@template\([\'"]?([\w]+)[\'"]?\)(.*?)@endtemplate/s', function($matches) use (&$templates) {
            list(, $name, $template) = $matches;
            $this->templates[$name] = call_user_func($this->compileString, trim($template));
            return '';
        }, $view);

        return $view;
    }

    /**
     * Get template by name
     *
     * @param string $name
     * @return string
     * @throws TemplateNotFoundException
     */
    public function getTemplate($name)
    {
        if (!array_key_exists($name, $this->templates)) {
            throw new TemplateNotFoundException("Template '${name}' not found.");
        }

        return $this->templates[$name];
    }

    /**
     * Render template by string passed to a "render" directive
     *
     * @param string $argsString
     * @return string
     * @throws InvalidDirectiveUsageException
     */
    public function renderTemplate($argsString)
    {
        list($blockName, $data) = $this->parseArgs($argsString);
        if (!$blockName) {
            throw new InvalidDirectiveUsageException('Invalid usage of @render directive.');
        }

        $str = [];
        $str[] = '<?php (function($vars) { ?>';
        $str[] = '<?php extract($vars); ?>';
        $str[] = $this->getTemplate($blockName);
        $str[] = "<?php })(array_merge(get_defined_vars(), ${data}))?>";

        return implode(PHP_EOL, $str);
    }

    /**
     * Extract from argument string template name and render arguments.
     *
     * @param string $argsString
     * @return array
     */
    private function parseArgs($argsString)
    {
        $matches = [];
        if (!preg_match('/^[\'"]?([\w]+)[\'"]?(\s*,\s*(.*))?$/s', $argsString, $matches)) {
            return [null, null];
        }

        $data = '[]';
        if (count($matches) === 4) {
            $data = $matches[3];
        }

        return [$matches[1], $data];
    }
}