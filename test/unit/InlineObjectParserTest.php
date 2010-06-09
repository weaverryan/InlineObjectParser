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
    $foo = new InlineObjectTypeFoo();
    $bar = new InlineObjectTypeBar();

    // Add a type via the constructor
    $parser = new InlineObjectParser(array(
      'testing_type' => $foo,
    ));

    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => $foo,
    ));
    $this->assertEquals($parser->getType('testing_type'), $foo);

    // Override the class of the existing type
    $parser->addType('testing_type', $bar);
    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => $bar,
    ));

    // Add another type
    $parser->addType('another_type', $foo);
    $this->assertEquals($parser->getTypes(), array(
      'testing_type' => $bar,
      'another_type' => $foo,
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
      'Foo with used option [foo:with_options extra="testing"]' => 'Foo with used option with_options_foo_testing',
      'Name with quotes: [foo:"with quotes"]' => 'Name with quotes: with quotes_foo',
    );

    $parser = new InlineObjectParser();
    $foo = new InlineObjectTypeFoo();
    $bar = new InlineObjectTypeBar();
    $parser->addType('foo', $foo);
    $parser->addType('bar', $bar);

    foreach ($results as $source => $expected)
    {
      $this->assertEquals($expected, $parser->parse($source));
    }
  }

  public function testParseTypes()
  {
    $parser = new InlineObjectParser();
    $foo = new InlineObjectTypeFoo();
    $bar = new InlineObjectTypeBar();
    $parser->addType('foo', $foo);
    $parser->addType('bar', $bar);

    $parsed = $parser->parseTypes('No embedded types');
    $this->assertEquals('No embedded types', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('With foo [foo:test]');
    $this->assertEquals('With foo %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals(array(new InlineObjectTypeFoo('test')), $parsed[1]);

    $parsed = $parser->parseTypes('With foo [foo:test] and bar [bar:test]');
    $objects = array(
      new InlineObjectTypeFoo('test'),
      new InlineObjectTypeBar('test'),
    );
    $this->assertEquals('With foo %%INLINE_OBJECT_0%% and bar %%INLINE_OBJECT_1%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Unrecognized type [other:test]');
    $this->assertEquals('Unrecognized type [other:test]', $parsed[0]);
    $this->assertEquals(array(), $parsed[1]);

    $parsed = $parser->parseTypes('Foo with options [foo:with_options bar=true]');
    $objects = array(
      new InlineObjectTypeFoo('with_options', array('bar' => true)),
    );
    $this->assertEquals('Foo with options %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);

    $parsed = $parser->parseTypes('Foo with options [foo:with_options label="my foo object"]');
    $objects = array(
      new InlineObjectTypeFoo('with_options', array('label' => 'my foo object')),
    );
    $this->assertEquals('Foo with options %%INLINE_OBJECT_0%%', $parsed[0]);
    $this->assertEquals($objects, $parsed[1]);
  }

  public function testCaching()
  {
    // Setup two parsers and compare results when caching is on for one of them
    $foo = new InlineObjectTypeFoo();
    $bar = new InlineObjectTypeBar();

    // Setup a stub, only stub the getCache() method
    $stub = $this->getMock('InlineObjectParser', array('getCache'));
    $stub->expects($this->any())
      ->method('getCache')
      ->will($this->returnValue(array(
        'cached test %%INLINE_OBJECT_0%% string',
        array(0 => new InlineObjectTypeFoo('test'))
      )));
    $stub->addType('foo', $foo);
    $stub->addType('bar', $bar);
    
    $parser = new InlineObjectParser();
    $parser->addType('foo', $foo);
    $parser->addType('bar', $bar);

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