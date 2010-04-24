<?php

/**
 * Toolkit for working with strings.
 * 
 * Taken from the symfony project (http://www.symfony-project.org)
 * 
 * @package     InlineObjectParser
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectToolkit
{
  /**
   * Converts string to array
   *
   * @param  string $string  the value to convert to array
   *
   * @return array
   */
  public static function stringToArray($string)
  {
    if (is_array($string))
    {
      return $string;
    }

    preg_match_all('/
      \s*(\w+)              # key                               \\1
      \s*=\s*               # =
      (\'|")?               # values may be included in \' or " \\2
      (.*?)                 # value                             \\3
      (?(2) \\2)            # matching \' or " if needed        \\4
      \s*(?:
        (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
      )
    /x', $string, $matches, PREG_SET_ORDER);

    $attributes = array();
    foreach ($matches as $val)
    {
      $attributes[$val[1]] = self::literalize($val[3]);
    }

    return $attributes;
  }

  /**
   * Finds the type of the passed value, returns the value as the new type.
   *
   * @param  string $value
   * @param  bool   $quoted  Quote?
   *
   * @return mixed
   */
  public static function literalize($value, $quoted = false)
  {
    // lowercase our value for comparison
    $value  = trim($value);
    $lvalue = strtolower($value);

    if (in_array($lvalue, array('null', '~', '')))
    {
      $value = null;
    }
    else if (in_array($lvalue, array('true', 'on', '+', 'yes')))
    {
      $value = true;
    }
    else if (in_array($lvalue, array('false', 'off', '-', 'no')))
    {
      $value = false;
    }
    else if (ctype_digit($value))
    {
      $value = (int) $value;
    }
    else if (is_numeric($value))
    {
      $value = (float) $value;
    }

    return $value;
  }

  /**
   * Translates an array of key value pairs into a string that can be
   * used as attributes in a tag
   * 
   * $arr = array('width' => 50);
   * echo '<img src="/images/my_img.jpg" '.InlineObjectToolkit::arrayToAttributes($arr).'/>';
   * 
   * $attrs = 'width="50"';
   * echo '<img src="/images/my_img.jpg" '.InlineObjectToolkit::attrs($arr).'/>';
   * 
   * @param mixed $attributes The array of attributes to translate or a string of attributes
   */
  public static function arrayToAttributes($attributes = array())
  {
    // Guarantee that attributes is an array
    $attributes = self::stringToArray($attributes);

    $html = '';
    foreach ($attributes as $key => $value)
    {
      $html .= ' '.$key.'="'.htmlspecialchars($value, ENT_COMPAT, 'utf-8').'"';
    }

    return $html;
  }
}