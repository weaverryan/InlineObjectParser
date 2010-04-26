<?php

/**
 * Capitalizes the given word.
 * 
 * Usage:
 * require_once '/path/to/lib/types/InlineObjectCapitalize.php';
 * 
 * $parser = new InlineObjectParser();
 * $parser->addType('caps', 'InlineObjectCapitalize');
 * $parser->parse('Capitalize the next [caps:word].');
 * 
 * @package     InlineObjectParser
 * @subpackage  types
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectCapitalize extends InlineObjectType
{
  public function render()
  {
    return strtoupper($this->getName());
  }
}