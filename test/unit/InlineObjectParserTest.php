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
    $foo = new InlineObjectTypeFoo('foo');
    $bar = new InlineObjectTypeBar('foo'); // purposefully made to be foo

    // Add a type via the constructor
    $parser = new InlineObjectParser(array($foo));

    $this->assertEquals($parser->getTypes(), array(
      'foo' => $foo,
    ));
    $this->assertEquals($parser->getType('foo'), $foo);

    // Override the class of the existing type
    $parser->addType($bar);
    $this->assertEquals($parser->getTypes(), array(
      'foo' => $bar,
    ));

    // Add another type
    $foo = new InlineObjectTypeFoo('bar'); // purposefully made to be bar
    $parser->addType($foo);
    $this->assertEquals($parser->getTypes(), array(
      'foo' => $bar,
      'bar' => $foo,
    ));
  }

  public function testParse()
  {
    $results = array(
      'No embedded types' => 'No embedded types',
      'With foo [foo:test]' => 'With foo test_foo',
      'With foo [foo:test] and bar [bar:test]' => 'With foo test_foo and bar test_bar',
      'Unrecognized type [other:test]' => 'Unrecognized type [other:test]',
      'Foo with arguments [foo:with_arguments bar=true]' => 'Foo with arguments with_arguments_foo',
      'Foo with arguments [foo:with_arguments label="my foo object"]' => 'Foo with arguments with_arguments_foo',
      'Foo with used argument [foo:with_arguments extra="testing"]' => 'Foo with used argument with_arguments_foo_testing',
      'Name with quotes: [foo:"with quotes"]' => 'Name with quotes: with quotes_foo',
    );

    $parser = new InlineObjectParser();
    $foo = new InlineObjectTypeFoo('foo');
    $bar = new InlineObjectTypeBar('bar');
    $parser->addType($foo);
    $parser->addType($bar);

    foreach ($results as $source => $expected)
    {
      $this->assertEquals($expected, $parser->parse($source));
    }
  }

  public function testParseTypes()
  {
    $parser = new InlineObjectParser();
    $foo = new InlineObjectTypeFoo('foo');
    $bar = new InlineObjectTypeBar('bar');
    $parser->addType($foo);
    $parser->addType($bar);

    $parsed = $parser->parseTypes('No embedded types');
    $this->assertEquals('No embedded types', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('With foo [foo:test]');
    $this->assertEquals('With foo %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals(array(
      array('type' => 'foo', 'name' => 'test', 'arguments' => array())
      ), $parsed[1]
    );
    
    $parsed = $parser->parseTypes('With foo [foo:test] and bar [bar:test]');
    $objects = array(
      array('type' => 'foo', 'name' => 'test', 'arguments' => array()),
      array('type' => 'bar', 'name' => 'test', 'arguments' => array()),
    );
    $this->assertEquals('With foo %%INLINE_OBJECT_0%% and bar %%INLINE_OBJECT_1%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Unrecognized type [other:test]');
    $this->assertEquals('Unrecognized type [other:test]', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('Foo with arguments [foo:with_arguments bar=true]');
    $objects = array(
      array('type' => 'foo', 'name' => 'with_arguments', 'arguments' => array('bar' => 'true'))
    );
    $this->assertEquals('Foo with arguments %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Foo with arguments [foo:with_arguments label="my foo object"]');
    $objects = array(
      array('type' => 'foo', 'name' => 'with_arguments', 'arguments' => array('label' => 'my foo object'))
    );
    $this->assertEquals('Foo with arguments %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);
  }

  public function testCaching()
  {
    // Setup two parsers and compare results when caching is on for one of them
    $foo = new InlineObjectTypeFoo('foo');
    $bar = new InlineObjectTypeBar('bar');

    // Setup a stub, only stub the getCache() method
    $stub = $this->getMock('InlineObjectParser', array('getCache'));
    $stub->expects($this->any())
      ->method('getCache')
      ->will($this->returnValue(array(
        'cached test %%INLINE_OBJECT_0%% string',
        array(0 => array('type' => 'foo', 'name' => 'test', 'arguments' => array()))
      )));
    $stub->addType($foo);
    $stub->addType($bar);
    
    $parser = new InlineObjectParser();
    $parser->addType($foo);
    $parser->addType($bar);

    // Test a basic string, caching automatically takes place
    $this->assertEquals($stub->parse('test string [foo:test]'), 'cached test test_foo string');

    // Now explicitly use the same key twice, but caching is off
    $this->assertEquals($parser->parse('test string', 'test_key'), 'test string');
    $this->assertEquals($parser->parse('test string2', 'test_key'), 'test string2');

    // Specify the same key twice wit caching on, returns the same results
    $this->assertEquals($stub->parse('test string', 'test_key'), 'cached test test_foo string');
    $this->assertEquals($stub->parse('test string2', 'test_key'), 'cached test test_foo string');
  }
}