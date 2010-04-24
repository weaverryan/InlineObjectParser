<?php

require_once dirname(__FILE__).'/../lib/phpunit/PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../lib/InlineObjectAutoloader.php';
InlineObjectAutoloader::register();

/**
 * Unit test for InlineObjectToolki
 * 
 * @package     InlineObjectParser
 * @subpackage  test
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectToolkitTest extends PHPUnit_Framework_TestCase
{
  public function testArrayToAttributes()
  {
    $results = array();
    
    $results[] = array(
      'source' => 'width="50"',
      'expected' => ' width="50"',
    );
    $results[] = array(
      'source' => 'width=50',
      'expected' => ' width="50"',
    );
    $results[] = array(
      'source' => 'width  =  50   height=100',
      'expected' => ' width="50" height="100"',
    );
    $results[] = array(
      'source' => array('width' => 50),
      'expected' => ' width="50"',
    );
    
    foreach ($results as $result)
    {
      $this->assertEquals($result['expected'], InlineObjectToolkit::arrayToAttributes($result['source']));
    }
  }
}