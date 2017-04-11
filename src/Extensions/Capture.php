<?php

namespace Pustato\LaravelBladeRenderFlow\Extensions;


use Pustato\LaravelBladeRenderFlow\Exceptions\BlockNotFoundException;

/**
 * Class Capture
 *
 * @package Pustato\LaravelBladeRenderFlow\Extensions
 */
class Capture
{
    /** @var array */
    private $blocks = [];

    /**
     * Get block content
     *
     * @param string $name
     * @return string
     * @throws BlockNotFoundException
     */
    public function getBlock($name)
    {
        if (!array_key_exists($name, $this->blocks)) {
            throw new BlockNotFoundException("Block '${name}' not found.");
        }

        return $this->blocks[$name];
    }

    /**
     * Set block content
     *
     * @param string $name
     * @param string $content
     * @return Capture
     */
    public function setBlock($name, $content)
    {
        $this->blocks[$name] = $content;
        return $this;
    }

    /**
     * Is block exists
     *
     * @param string $name
     * @return bool
     */
    public function hasBlock($name)
    {
        return array_key_exists($name, $this->blocks);
    }

    /**
     * Clear block content
     *
     * @param string $name
     * @return Capture
     */
    public function clearBlock($name)
    {
        if (array_key_exists($name, $this->blocks)) {
            unset($this->blocks[$name]);
        }
        return $this;
    }

    /**
     * Clear all blocks content
     */
    public function clear()
    {
        $this->blocks = [];
    }

    /**
     * Generate callback function for output buffering function
     *
     * @param string $blockName
     * @return \Closure
     */
    public function getOBCallback($blockName)
    {
        return function ($buffer) use ($blockName) {
            $this->setBlock($blockName, $buffer);
            return $buffer;
        };
    }
}
