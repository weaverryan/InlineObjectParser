<?php

require_once dirname(__FILE__).'/../lib/phpunit/PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../lib/InlineObjectAutoloader.php';
InlineObjectAutoloader::register();

/**
 * Unit test for InlineObject
 * 
 * @package     InlineObjectParser
 * @subpackage  test
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectTypeTest extends PHPUnit_Framework_TestCase
{
  public function testGetOption()
  {
    $stub = $this->_createStub();

    // option1 exists and equals foo
    $this->assertEquals($stub->getOption('option1'), 'foo');
    $this->assertEquals($stub->getOption('option1', 'bar'), 'foo');

    // option 2 exists, but it's set to null, the default is returned
    $this->assertEquals($stub->getOption('option2'), null);
    $this->assertEquals($stub->getOption('option2', 'bar'), 'bar');

    // option 3 does not exist, it returns the default
    $this->assertEquals($stub->getOption('option3'), null);
    $this->assertEquals($stub->getOption('option3', 'bar'), 'bar');
  }

  public function testGetName()
  {
    $stub = $this->_createStub();
    
    $this->assertEquals($stub->getName(), 'type_name');
  }

  public function testToString()
  {
    $stub = $this->_createStub();
    
    $this->assertEquals((string) $stub, 'rendered');
  }

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