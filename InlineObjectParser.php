<?php

/**
 * Parses a string and converts inline syntax into InlineObject instances
 * 
 * This takes in raw input and detects inline object syntax. The inline
 * object syntax are converted into InlineObject instances, which are
 * then rendered.
 * 
 * The final output is a processed string
 * 
 * @package     InlineObjectParser
 * @author      Ryan Weaver <ryan@thatsquality.com>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 */

class InlineObjectParser
{
  /**
   * @var array A map of type => class that identifies the class to use
   *            for each inline type
   */
  protected $_types = array();

  /**
   * Class constructor
   */
  public function __construct($types = array())
  {
    $this->_types = $types;
  }

  /**
   * Parses raw text and returns the processed result
   * 
   * @param string $text The raw text that should be processed
   */
  public function parse($text)
  {
    return $text;
  }

  /**
   * Add a object type to be processed
   * 
   * @example
   * $parser->addType('image', 'InlineObjectImage');
   * 
   * @param string $name  The name by which the type will be identified when
   *                      written inline
   * @param string $class The InlineObject class that will render the type
   */
  public function addType($name, $class)
  {
    $this->_types[$name] = $class;
  }
}