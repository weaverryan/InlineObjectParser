<?php

/**
 * Calls a PHP function with a maximum of one argument.
 * 
 * Usage:
 * require_once '/path/to/lib/types/InlineObjectFunction.php';
 * 
 * $parser = new InlineObjectParser();
 * $parser->addType('fxn', 'InlineObjectFunction');
 * echo $parser->parse('[fxn:cos arg=3.1415926]');
 * 
 * @package     InlineObjectParser
 * @subpackage  types
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectFunction extends InlineObjectType
{
  public function render()
  {
    return call_user_func($this->getName(), $this->getOption('arg'));
  }
}