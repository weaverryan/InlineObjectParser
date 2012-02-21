<?php

namespace InlineObject;

/**
 * Represents an object that is constructed using inline code
 *
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
interface TypeInterface
{
    /**
     * Renders the object using the given name and options properties
     *
     * @param string $name The name/key for the inline object
     * @param array $args The inline options for this object
     */
    function render($name, $args);

    /**
     * Returns the name of this inline object type
     *
     * @return string
     */
    function getName();

    /**
     * @param  string $name The name of the option to set
     * @param mixed $value The value for the option
     * @return void
     */
    public function setOption($name, $value);
}