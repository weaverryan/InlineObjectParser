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
    
    $this->_initialize();
  }

  /**
   * Initialize the parser
   */
  protected function _initialize()
  {
  }

  /**
   * Parses raw text and returns the processed result
   * 
   * @param string $text The raw text that should be processed
   * @param string $key An optional key to use for caching
   */
  public function parse($text, $key = null)
  {
    // Parse the string to retrieve tokenized text and an array of InlineObjects
    $parsed = $this->parseTypes($text, $key);

    $text = $parsed[0];
    $objects = $parsed[1];

    // Create an array of the text from the rendered objects
    $renderedObjects = array();
    foreach ($objects as $object)
    {
      $renderedObjects[] = $object->render();
    }

    return $this->_combineTextAndRenderedObjects($text, $renderedObjects);
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

  /**
   * Takes in the tokenized string and inline objects and returns the
   * fully-processed string
   */
  protected function _combineTextAndRenderedObjects($text, $renderedObjects)
  {
    // Call sprintf using the rendered objects to get the final, processed text
    return call_user_func_array('sprintf', array_merge(array($text), $renderedObjects));
  }

  /**
   * Parses raw text and returns a tokenized string and an array of InlineObjects
   * 
   * array(
   *   0 => 'The inline object with tokens like this %s and this %s',
   *   1 => array(
   *     0 => InlineObject instance
   *     1 => InlineObject instance
   *   )
   * )
   *
   * This will not commonly be used directly, unless you need a lower-level
   * of granularity on the parsing.
   * 
   * @return array The array containing the string and the InlineObjects
   */
  public function parseTypes($text, $cacheKey = null)
  {
    if ($cacheKey === null)
    {
      $cacheKey = md5($text);
    }

    // Check for a cached result
    if ($parsed = $this->getCache($cacheKey))
    {
      return $parsed;
    }

    $matches = array();
    preg_match_all($this->_getTypeRegex(), $text, $matches);

    // If no matches found, return array with just the raw text
    if (!isset($matches[0]) || !$matches[0])
    {
      return array($text, array());
    }

    $types = $matches[1];
    $bodies = $matches[2];

    $inlineObjects = array();

    foreach ($bodies as $key => $body)
    {
      $type = $types[$key];
      $class = $this->getTypeClass($type);

      if (!$class)
      {
        throw new Exception(sprintf('Cannot process type %s. No InlineObject class found', $type));
      }

      // Determine if the name was wrapped in quotes and handle
      if (strpos($body, '"') === 0)
      {
        // Split on quotes, the name will be the second entry (the first is blank)
        $e = explode('"', $body);
        $name = $e[1];
        unset($e[0], $e[1]);
        
        $optionsString = implode('"', $e);
      }
      else
      {
        // Split on spaces, the name will be the first entry
        $e = explode(' ', $body);
        $name = $e[0];
        unset($e[0]);
        
        $optionsString = implode(' ', $e);
      }

      $options = InlineObjectToolkit::stringToArray($optionsString);

      $inlineObject = new $class($name, $options);

      // Store the object and replace the text with a token
      $inlineObjects[] = $inlineObject;
      $text = str_replace($matches[0][$key], '%s', $text);
    }

    $parsed = array($text, $inlineObjects);

    // Set the parsed object to cache
    $this->setCache($cacheKey, $parsed);

    return $parsed;
  }

  /**
   * Returns the class name for the given type
   * 
   * @return string or null
   */
  public function getTypeClass($type)
  {
    return isset($this->_types[$type]) ? $this->_types[$type] : null;
  }

  /**
   * Returns the array of type => class entries that will be processed
   * 
   * @return array
   */
  public function getTypes()
  {
    return $this->_types;
  }

  /**
   * Returns the regular expression used to match the inline objects
   * 
   * @return string
   */
  protected function _getTypeRegex()
  {
    $typesMatch = implode('|', array_keys($this->_types));

    return '/\[('.$typesMatch.'):(.*?)\]/';
  }

  /**
   * Returns the cached parse result for a given job.
   * 
   * The object that's cached is an array that matches the result of parseTypes()
   * 
   * This should be overrridden if you need to cache parsing
   * 
   * @param string $key The key of the cache to retrieve
   * @return array or false
   */
  public function getCache($key)
  {
    return false;
  }

  /**
   * Puts the parsed array into cache.
   * 
   * This should be overrridden if you need to cache parsing
   * 
   * @param string $key The key to give this cache object
   * @param array $data The data array to cache
   */
  public function setCache($key, $data)
  {
  }
}