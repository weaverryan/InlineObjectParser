InlineObjectParser
==================

A library to allow for simple objects to be written in plain text and then
translated and ultimately rendered in any way.

    Display a [image:banana.png width="50"] image.
    Display a <img src="/images/banana.png" width="50" /> image.

The common inline syntax is passed to an object where it can be rendered
in any way. This is infinitely configurable and expandle. For example,
consider the following possibilities.

    Show me the current year: [date:year]
    Show me the current year: 2010

    I could even [translate:"translate a phrase" from=en to=es] into spanish.
    I could even traducir una frase into spanish.

    Return only a "[substring:portion length=4 start=2]" of a word.
    Return only a "rtio" of a word.

    Capitalize the next [caps:word].
    Capitalize the next WORD.

    Show me pi: [const:M_PI].
    Show me pi: 3.1415926535898.

    Show me the cosine of a number: [fxn:cos arg=3.1415926].
    Show me the cosine of a number: -1.

See the [examples.php](http://github.com/weaverryan/InlineObjectParser/blob/master/types/example.php)
file in the `types` directory for working examples of each of the above.

Usage
-----

To parse a particular string, simply create a parser object and pass the
raw text to the ->parse() function:

    require_once '/path/to/lib/InlineObjectAutoloader.php';
    InlineObjectAutoloader::register();

    $parser = new InlineObjectParser();
    $type = new InlineObjectImageType('image');
    $parser->addType($type); // register some type (more details below)

    echo $parser->parse('Display a [image:banana.png width="50"] image.');

Creating Syntax Types
---------------------

In order to use the parser, you'll need to map each type (e.g. `image`) to
a class that will handle the rendering for that type.

Each inline type is rendered by a class that extends `InlineObjectType`
(which is an abstract class). To define a new inline type, create a subclass
of `InlineObjectType` and define how the type should be rendered.

    class InlineObjectImage extends InlineObjectType
    {
      public function render($name, $arguments)
      {
        $url = '/images/'.$name;
        
        return sprintf(
          '<img src="%s"%s />',
          $url,
          InlineObjectToolkit::arrayToAttributes($arguments)
        );
      }
    }

To use the new type, simply tell the parser about it via the `register()`
method.

    $parser = new InlineObjectParser();
    $type = new InlineObjectImageType('image');
    $parser->register($type);
    
    echo $parser->parse('Display a [image:banana.png width="50"] image.');

Caching
-------

With large text, the regex needed to process the inline objects can take
a toll on performance. Fortunately, caching the regex parsing is quite easy.

The `InlineObjectParser` class exposes two caching methods: `getCache()`
and `setCache()`. By default, these methods do nothing - they are stubs
that you can use to do any type of caching you may need.

To activate caching, create and use a subclass of `InlineObjectParser`. In
this class, override `getCache()` and `setCache()`:

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