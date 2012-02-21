<?php

namespace InlineObject;

/**
 * Represents an object that is constructed using inline code
 *
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
abstract class Type implements TypeInterface
{
  /**
   * @var string
   * @var array
   */
  protected
    $_name,
    $_options;

  /**
   * Class constructor
   *
   * @param string $name The name/key that this type is matching to
   * @param array $options Options specific to be used when rendering this type
   */
  public function __construct($name, $options = array())
  {
    $this->_name = $name;
    $this->_options = $options;
  }

  /**
   * Returns the name/key for this type.
   *
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * Returns the options array for this 
   *
   * @return array
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Returns a specific option, or the default value if the option does not exist
   *
   * @return mixed
   */
  public function getOption($name, $default = null)
  {
    return isset($this->_options[$name]) ? $this->_options[$name] : $default;
  }

  /**
   * @param  string $name The name of the option to set
   * @param mixed $value The value for the option
   * @return void
   */
  public function setOption($name, $value)
  {
    $this->_options[$name] = $value;
  }
}