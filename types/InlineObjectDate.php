<?php

/**
 * Returns the current date in a variety of possible formats.
 * 
 * Usage:
 * require_once '/path/to/lib/types/InlineObjectDate.php';
 * 
 * $parser = new InlineObjectParser();
 * $parser->addType('date', 'InlineObjectDate');
 * echo $parser->parse('[date:year]');
 * 
 * @package     InlineObjectParser
 * @subpackage  types
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectDate extends InlineObjectType
{
  public function render($name, $options)
  {
    switch ($name)
    {
      case 'year':
        return date('Y');
      
      case 'date':
        return date('M jS Y');
      
      case 'time':
        return date('g:i a');
      
      default:
        return date('M jS Y').' at '.date('g:i a');
    }
  }
}