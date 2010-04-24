<?php

require_once dirname(__FILE__).'/../lib/phpunit/PHPUnit/Framework.php';
require_once dirname(__FILE__).'/../../lib/InlineObjectAutoloader.php';
InlineObjectAutoloader::register();

// Load in the some dummy stub classes
require_once dirname(__FILE__).'/stub/InlineObjectTypeFoo.php';
require_once dirname(__FILE__).'/stub/InlineObjectTypeBar.php';


/**
 * Unit test for InlineObjectParser
 * 
 * @package     InlineObjectParser
 * @subpackage  test
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectParserTest extends PHPUnit_Framework_TestCase
{
  public function testTypeMutations()
  {
    // Add a type via the constructor
    $parser = new InlineObjectParser(array(
      'testing_type' => 'InlineObjectTestingType',
    ));

    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => 'InlineObjectTestingType',
    ));
    $this->assertEquals($parser->getTypeClass('testing_type'), 'InlineObjectTestingType');

    // Override the class of the existing type
    $parser->addType('testing_type', 'NewTypeClass');
    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => 'NewTypeClass',
    ));

    // Add another type
    $parser->addType('another_type', 'AnotherTypeClass');
    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => 'NewTypeClass',
      'another_type' => 'AnotherTypeClass',
    ));
  }

  public function testParse()
  {
    $results = array(
      'No embedded types' => 'No embedded types',
      'With foo [foo:test]' => 'With foo test_foo',
      'With foo [foo:test] and bar [bar:test]' => 'With foo test_foo and bar test_bar',
      'Unrecognized type [other:test]' => 'Unrecognized type [other:test]',
      'Foo with options [foo:with_options bar=true]' => 'Foo with options with_options_foo',
      'Foo with options [foo:with_options label="my foo object"]' => 'Foo with options with_options_foo',
    );

    $parser = new InlineObjectParser();
    $parser->addType('foo', 'InlineObjectTypeFoo');
    $parser->addType('bar', 'InlineObjectTypeBar');

    foreach ($results as $source => $expected)
    {
      $this->assertEquals($expected, $parser->parse($source));
    }
  }

  public function testParseTypes()
  {
    $parser = new InlineObjectParser();
    $parser->addType('foo', 'InlineObjectTypeFoo');
    $parser->addType('bar', 'InlineObjectTypeBar');

    $parsed = $parser->parseTypes('No embedded types');
    $this->assertEquals('No embedded types', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('With foo [foo:test]');
    $this->assertEquals('With foo %s', $parsed[0]);
    $this->assertEquals(array(new InlineObjectTypeFoo('test')), $parsed[1]);

    $parsed = $parser->parseTypes('With foo [foo:test] and bar [bar:test]');
    $objects = array(
      new InlineObjectTypeFoo('test'),
      new InlineObjectTypeBar('test'),
    );
    $this->assertEquals('With foo %s and bar %s', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Unrecognized type [other:test]');
    $this->assertEquals('Unrecognized type [other:test]', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('Foo with options [foo:with_options bar=true]');
    $objects = array(
      new InlineObjectTypeFoo('with_options', array('bar' => true)),
    );
    $this->assertEquals('Foo with options %s', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Foo with options [foo:with_options label="my foo object"]');
    $objects = array(
      new InlineObjectTypeFoo('with_options', array('label' => 'my foo object')),
    );
    $this->assertEquals('Foo with options %s', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);
  }
}