InlineObjectParser
==================

A library to allow for simple objects to be written in plain text and then
translated and ultimately rendered in any way.

    Display a [image:banana.png width="50"] image.
    Display a <img src="/images/banana.png" width="50" /> image.

The common inline syntax is passed to an object where it can be rendered
in any way. This is infinitely configurable and expandle. For example,
consider the following possibilities.

    Show me pi: [const:M_PI].
    Show me pi: 3.1415926535898.

    Show me the cosine of a number: [fxn:cos arg=3.1415926].
    Show me the cosine of a number: -1.

    Show me the current year: [date:year]
    Show me the current year: 2010

    Capitalize the next [caps:word].
    Capitalize the next WORD.

    Return only a "[substring:portion length=4 start=2]" of a word.
    Return only a "rtio" of a word.

    I could even [translate:"translate a phrase" from=en to=es] into spanish.
    I could even traducir una frase into spanish.

Usage
-----

To parse a particular string, simply create a parser object and pass it
to the ->parse() function.

    require_once '/path/to/lib/InlineObjectAutoloader.php';
    InlineObjectAutoloader::register();

    $parser = new InlineObjectParser();
    $parser->addType('image', 'InlineObjectImage'); // register any types

    echo $parser->parse('Display a [image:banana.png width="50"] image.');

Creating Syntax Types
---------------------

In order to use the parser, you'll need to map each type (e.g. `image`) to
a class that will handle the rendering for that type.

Each inline syntax corresponds to an instance of `InlineObjectType`, which is
an abstract class. To define a new syntax, create a subclass of
`InlineObjectType` and define how it should be rendered.

    class InlineObjectImage extends InlineObjectType
    {
      public function render()
      {
        $url = '/images/'.$this->getName();
        
        return sprintf(
          '<img src="%s"%s />',
          $url,
          InlineObjectToolkit::arrayToAttributes($this->getOptions())
        );
      }
    }

To use the new type, you simply need to tell the parser about it with the
`register()` method.

    $parser = new InlineObjectParser();
    $parser->register('image', 'InlineObjectImage');
    
    echo $parser->parse('Display a [image:banana.png width="50"] image.');

Caching
-------

With large text, the regex needed to process in the inline objects can take
a toll on performance. Fortunately, caching the regex parsing is quite easily.

The `InlineObjectParser` class exposes two caching methods: `getCache()`
and `setCache()`. By default, these methods do nothing - they are stubs
that you can use to do any type of caching you need.

To activate caching, create and use a subclass of `InlineObjectParser`. In
this class, override `getCache()` and `setCache()` to cache as you please:

    class InlineObjectCacheableParser extends InlineObjectParser
    {
      public function getCache($key)
      {
        return unserialize(file_get_contents('/tmp/'.$key));
      }
      
      public function setCache($key, $data)
      {
        file_put_contents('/tmp/'.$key, serialize($data));
      }
    }

More Information
----------------

This library was originally extracted from [sympal CMF](http://www.sympalphp.org)
and is intended to be used as a standalone library for embedding simple
inline objects. The original code was written by Jon Wage and Ryan Weaver.

The library is complete with unit tests. To see a practical use of this
library, see the symfony plugin [sfInlineObjectPlugin](http://github.com/weaverryan/sfInlineObjectPlugin).

If you have comments, questions or would like to contribute, feel free to
contact me at ryan[at]thatsquality.com