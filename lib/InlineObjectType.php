<?php

/**
 * Represents an object that is constructed using inline code
 * 
 * @package     InlineObjectParser
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

abstract class InlineObjectType
{

  /**
   * Class constructor
   */
  public function __construct()
  {
  }

  /**
   * Renders the object using the given name and options properties
   *
   * @param string $name The name/key for the inline object
   * @param array $options The inline options for this object
   */
  abstract public function render($name, $options);
}