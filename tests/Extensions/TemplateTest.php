<?php

namespace Extensions;


use PHPUnit\Framework\TestCase;
use Pustato\LaravelBladeRenderFlow\Exceptions\InvalidDirectiveUsageException;
use Pustato\LaravelBladeRenderFlow\Exceptions\TemplateNotFoundException;
use Pustato\LaravelBladeRenderFlow\Extensions\Template;

class TemplateTest extends TestCase
{
    /** @var Template */
    private $templateInstance;

    /** @inheritdoc */
    public function setUp()
    {
        $this->templateInstance = new Template(function($str) {
            return 'compiled!'.PHP_EOL.$str;
        });
    }

    /**
     * Return test view file with "template" directive
     *
     * @return string
     */
    private function getViewWithTemplate()
    {
        return file_get_contents(__DIR__ . '/../stubs/view-with-templates');
    }

    /**
     * Return test view file without "template" directive
     *
     * @return string
     */
    private function getViewWithoutTemplate()
    {
        return file_get_contents(__DIR__ . '/../stubs/view-without-templates');
    }

    /**
     * @covers Template::extractTemplates()
     * @covers Template::getTemplate()
     */
    public function test_extract_template_from_view()
    {
        $view = $this->getViewWithTemplate();
        $this->assertContains('@template', $view);
        $this->assertContains('@endtemplate', $view);

        $processedView = $this->templateInstance->extractTemplates($view);

        $this->assertNotEquals($view, $processedView);
        $this->assertNotContains('@template', $processedView);
        $this->assertNotContains('@endtemplate', $processedView);

        $tpl1 = $this->templateInstance->getTemplate('t1');
        $tpl2 = $this->templateInstance->getTemplate('t2');

        $this->assertTrue(is_string($tpl1));
        $this->assertTrue(is_string($tpl2));

        $this->assertContains('tpl 1 line', $tpl1);
        $this->assertContains('tpl 2 line', $tpl2);
    }

    /**
     * @covers Template::extractTemplates()
     */
    public function test_not_modify_view_without_templates()
    {
        $view = $this->getViewWithoutTemplate();
        $this->assertNotContains('@template', $view);
        $this->assertNotContains('@endtemplate', $view);

        $processedView = $this->templateInstance->extractTemplates($view);

        $this->assertEquals($view, $processedView);
    }

    /**
     * @covers Template::getTemplate()
     */
    public function test_get_unknown_template_throws_exception()
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessageRegExp('/some_template/');
        $this->templateInstance->getTemplate('some_template');
    }

    /**
     * @covers Template::renderTemplate()
     */
    public function test_template_render()
    {
        $view = $this->getViewWithTemplate();
        $this->templateInstance->extractTemplates($view);

        $tpl1 = $this->templateInstance->renderTemplate("t1, ['arg' => 'val']");
        $this->assertTrue(is_string($tpl1));
        $this->assertContains('compiled!', $tpl1);
        $this->assertContains("['arg' => 'val']", $tpl1);

        $tpl2 = $this->templateInstance->renderTemplate("t2");
        $this->assertTrue(is_string($tpl2));
        $this->assertContains('compiled!', $tpl2);
        $this->assertNotContains("['arg' => 'val']", $tpl2);
    }

    /**
     * @covers Template::renderTemplate()
     */
    public function test_render_throws_exception_on_unknown_template()
    {
        $view = $this->getViewWithTemplate();
        $this->templateInstance->extractTemplates($view);

        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessageRegExp('/t3/');
        $this->templateInstance->renderTemplate('t3');
    }

    /**
     * @covers Template::renderTemplate()
     */
    public function test_render_throws_exception_on_invalid_arguments()
    {
        $view = $this->getViewWithTemplate();
        $this->templateInstance->extractTemplates($view);

        $this->expectException(InvalidDirectiveUsageException::class);
        $this->expectExceptionMessage('Invalid usage of @render directive.');
        $this->templateInstance->renderTemplate('some invalid args');
    }
}