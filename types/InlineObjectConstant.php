<?php

/**
 * Outputs the PHP constant that matches the name of the inline object
 * 
 * Usage:
 * require_once '/path/to/lib/types/InlineObjectConstant.php';
 * 
 * $parser = new InlineObjectParser();
 * $parser->addType('const', 'InlineObjectConstant');
 * echo $parser->parse('[const:M_PI]');
 * 
 * @package     InlineObjectParser
 * @subpackage  types
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectConstant extends InlineObjectType
{
  public function render($name, $arguments)
  {
    return constant($name);
  }
}