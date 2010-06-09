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
   * @var string
   */
  protected $_name;

  /**
   * Class constructor
   *
   * @param string $name The name/key that this type is matching to
   */
  public function __construct($name)
  {
    $this->_name = $name;
  }

  /**
   * Renders the object using the given name and options properties
   *
   * @param string $name The name/key for the inline object
   * @param array $options The inline options for this object
   */
  abstract public function render($name, $options);

  /**
   * Returns the name/key for this type.
   *
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }
}