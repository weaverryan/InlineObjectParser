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
  public function testGetName()
  {
    $type = $this->_createStub();
    $this->assertEquals('type_name', $type->getName());
  }

  protected function _createStub($name = 'type_name')
  {
    $stub = $this->getMockForAbstractClass('InlineObjectType', array(
      'type_name',
    ));
    $stub->expects($this->any())
      ->method('render')
      ->will($this->returnValue('rendered'));
    
    return $stub;
  }
}