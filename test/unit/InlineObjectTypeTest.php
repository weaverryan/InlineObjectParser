<?php

require_once dirname(__FILE__).'/../lib/phpunit/PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../lib/InlineObjectAutoloader.php';
InlineObjectAutoloader::register();

/**
 * Unit test for InlineObjectType
 * 
 * @package     InlineObjectParser
 * @subpackage  test
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectTypeTest extends PHPUnit_Framework_TestCase
{
  protected function _createStub()
  {
    $stub = $this->getMockForAbstractClass('InlineObjectType', array(
      'type_name',
      array(
        'option1' => 'foo',
        'option2' => null,
      )
    ));
    $stub->expects($this->any())
      ->method('render')
      ->will($this->returnValue('rendered'));
    
    return $stub;
  }
}