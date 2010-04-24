InlineObjectParser
==================

A library to allow for simple objects to be written in plain text and then
translated and ultimately rendered.

    Display a [image:banana.png width="50"] image.
    
    Display a <img src="/images/banana.png" width="50" /> image.



    Just display the url: [image:banana.png link="true"].
    
    Just display the url: /images/banana.png.

Usage
-----

To parse a particular string, simply create a parser object and pass it
to the ->parse() function.

    $parser = new InlineObjectParser();
    echo $parser->parse('Display a [image:banana.png width="50"] image.');

Creating Syntax Types
---------------------

In order to use the parser, you'll need to map each type (e.g. `image`) to
a class that will handle the rendering for that type.

Each inline syntax corresponds to an instance of `InlineObject`, which is
an abstract class. To define a new syntax, create a subclass of
`InlineObject` and define how it should be rendered.

    class InlineObjectImage
    {
      public function render()
      {
        $url = '/images/'.$this->identifier;
        
        if ($this->getOption('link'))
        {
          return $url;
        }
        else
        {
          return sprintf('<img src="%s" %s />', $url, self::optionsToAttributes($this->getOptions()));
        }
      }
    }

Next, just tell the parser about the new syntax.

    $parser = new InlineObjectParser();
    $parser->register('image', 'InlineObjectImage');
    
    echo $parser->parse('Display a [image:banana.png width="50"] image.');