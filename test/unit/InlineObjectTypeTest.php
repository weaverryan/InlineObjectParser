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
  public function testGettersSetters()
  {
    $type = $this->_createStub('type_name', array('test_option' => 'test_value'));
    $this->assertEquals('type_name', $type->getName());

    // get some options
    $this->assertEquals(array('test_option' => 'test_value'), $type->getOptions());
    $this->assertEquals('test_value', $type->getOption('test_option', 'default'));
    // test a non-existent option
    $this->assertEquals('default', $type->getOption('fake_value', 'default'));

    // set an option
    $type->setOption('new_option', 'new_value');
    $this->assertEquals('new_value', $type->getOption('new_option'));
  }

  protected function _createStub($name = 'type_name', $options = array())
  {
    $stub = $this->getMockForAbstractClass('InlineObjectType', array(
      'type_name',
      $options,
    ));
    $stub->expects($this->any())
      ->method('render')
      ->will($this->returnValue('rendered'));
    
    return $stub;
  }
}