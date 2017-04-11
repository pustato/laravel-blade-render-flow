<?php

namespace Extensions;


use PHPUnit\Framework\TestCase;
use Pustato\LaravelBladeRenderFlow\Exceptions\BlockNotFoundException;
use Pustato\LaravelBladeRenderFlow\Extensions\Capture;

class CaptureTest extends TestCase
{

    /** @var Capture */
    private $captureInstance;

    /** @inheritdoc */
    public function setUp()
    {
        $this->captureInstance = new Capture();
    }

    /**
     * @covers Capture::setBlock()
     * @covers Capture::getBlock()
     */
    public function test_store_block()
    {
        $this->assertFalse($this->captureInstance->hasBlock('name'));
        $this->assertFalse($this->captureInstance->hasBlock('another_content'));

        $this->captureInstance->setBlock('name', 'content');
        $this->captureInstance->setBlock('another_name', 'another_content');

        $this->assertTrue($this->captureInstance->hasBlock('name'));
        $this->assertTrue($this->captureInstance->hasBlock('another_name'));

        $this->assertEquals('content', $this->captureInstance->getBlock('name'));
        $this->assertEquals('another_content', $this->captureInstance->getBlock('another_name'));
    }

    /**
     * @covers Capture::getBlock()
     */
    public function test_getBlock_throws_exception_on_unknown_block()
    {
        $this->expectException(BlockNotFoundException::class);
        $this->captureInstance->getBlock('what?');
    }

    /**
     * @covers Capture::clearBlock()
     */
    public function test_clearBlock_deletes_block()
    {
        $this->captureInstance->setBlock('name__', 'content');
        $this->assertEquals('content', $this->captureInstance->getBlock('name__'));

        $this->captureInstance->clearBlock('name__');

        $this->assertFalse($this->captureInstance->hasBlock('name__'));
    }

    /**
     * @covers Capture::clearBlock()
     */
    public function test_clear_deletes_all_blocks()
    {
        $this->captureInstance->setBlock('block1', 'content1');
        $this->captureInstance->setBlock('block2', 'content2');

        $this->captureInstance->clear();

        $this->assertFalse($this->captureInstance->hasBlock('block1'));
        $this->assertFalse($this->captureInstance->hasBlock('block2'));
    }

    /**
     * @covers Capture::getOBCallback()
     */
    public function test_getOBCallback_storesBlock()
    {
        $callback = $this->captureInstance->getOBCallback('block_name');
        $this->assertTrue(is_callable($callback));

        $callback('content');
        $this->assertEquals('content', $this->captureInstance->getBlock('block_name'));
    }
}