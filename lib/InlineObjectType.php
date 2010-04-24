<?php

/**
 * Represents an object that is constructed using inline code
 * 
 * @package     InlineObject
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

abstract class InlineObjectType
{

  /**
   * @var mixed $_name Some sort of main identifier for the object
   * @var array $_options   An array of options for the object
   */
  protected
    $_name,
    $_options;

  /**
   * Class constructor
   */
  public function __construct($name = null, $options = array())
  {
    $this->_name = $name;
    $this->_options = $options;
  }

  /**
   * Renders the object using the given name and options properties
   */
  abstract public function render();

  /**
   * Returns an option value, or the given default if not found
   * 
   * @param string $name    The name of the option to return
   * @param mixed  $default The value to return if the option does not exist
   * 
   * @return mixed
   */
  public function getOption($name, $default = null)
  {
    return isset($this->_options[$name]) ? $this->_options[$name] : $default;
  }

  /**
   * Returns the array of options
   * 
   * @return array
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Returns the name of this object
   * 
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * @return array
   */
  public function __toString()
  {
    return $this->render();
  }
}