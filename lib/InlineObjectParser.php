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
   * An array of InlineObjectType objects where the key is the inline key
   *
   * @var array
   */
  protected $_types = array();

  /**
   * Class constructor
   */
  public function __construct($types = array())
  {
    foreach ($types as $type)
    {
      $this->addType($type);
    }
    
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
    $inlineObjectsArr = $parsed[1]; // array with type, name, and arguments keys

    // Create an array of the text from the rendered objects
    $renderedObjects = $this->_renderInlineObjectsFromArray($inlineObjectsArr);

    return $this->_combineTextAndRenderedObjects($text, $renderedObjects);
  }

  /**
   * Add a object type to be processed
   * 
   * @example
   * $parser->addType('image', $inlineType);
   * 
   * @param InlineObjectType $Type The InlineObject class that will render the type
   */
  public function addType(InlineObjectType $type)
  {
    $this->_types[$type->getName()] = $type;
  }

  /**
   * Called by parse to take an array of the inline objects array, and
   * return an array of each one rendered
   *
   * @throws sfException
   * @param  array $inlineObjects The inline objects array (keys: type, name, arguments)
   * @return array
   */
  protected function _renderInlineObjectsFromArray($inlineObjects)
  {
    $renderedObjects = array();
    foreach ($inlineObjects as $key => $inlineObject)
    {
      $typeObject = $this->getType($inlineObject['type']);
      if (!$typeObject)
      {
        throw new sfException(sprintf('No inline object type defined for "%s"', $inlineObject['type']));
      }

      $renderedObjects[$key] = $typeObject->render(
        $inlineObject['name'],
        $inlineObject['arguments']
      );
    }

    return $renderedObjects;
  }

  /**
   * Takes in the tokenized string and inline objects and returns the
   * fully-processed string
   */
  protected function _combineTextAndRenderedObjects($text, $renderedObjects)
  {
    foreach ($renderedObjects as $key => $renderedObject)
    {
      $text = str_replace(self::_generateInlineToken($key), $renderedObject, $text);
    }
    
    return $text;
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
    if ($cacheKey && $parsed = $this->getCache($cacheKey))
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
      // Determine if the name was wrapped in quotes and handle
      if (strpos($body, '"') === 0)
      {
        // Split on quotes, the name will be the second entry (the first is blank)
        $e = explode('"', $body);
        $name = $e[1];
        unset($e[0], $e[1]);
        
        $argumentsString = implode('"', $e);
      }
      else
      {
        // Split on spaces, the name will be the first entry
        $e = explode(' ', $body);
        $name = $e[0];
        unset($e[0]);
        
        $argumentsString = implode(' ', $e);
      }

      $arguments = InlineObjectToolkit::stringToArray($argumentsString);

      // create an incrementing key for replacement later
      $objectKey = self::_generateInlineToken($key);

      // Store the inline object information in the array
      $inlineObjects[$key] = array(
        'type'    => $types[$key],
        'name'    => $name,
        'arguments' => $arguments,
      );
      $text = str_replace($matches[0][$key], $objectKey, $text);
    }

    $parsed = array($text, $inlineObjects);

    // Set the parsed object to cache
    $this->setCache($cacheKey, $parsed);

    return $parsed;
  }

  /**
   * Returns the InlineObjectType connected with a given name/key
   *
   * @param  string $name The name/key corresponding to the type
   * @return InlineObjectType
   */
  public function getType($name)
  {
    return isset($this->_types[$name]) ? $this->_types[$name] : null;
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

  /**
   * Returns the inline token based on the given token number
   * 
   * @return string
   */
  protected static function _generateInlineToken($num)
  {
    return '%%INLINE_OBJECT_'.$num.'%%';
  }
}