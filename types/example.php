<?php

/*
 * This is a simple script to show the example types in actions
 * 
 * These types aren't meant to be product-ready, or even a good idea.
 * They're merely examples of different inline object types.
 */

require_once dirname(__FILE__).'/../lib/InlineObjectAutoloader.php';
InlineObjectAutoloader::register();

/*
 * 1) The Capitalize type
 */
require_once dirname(__FILE__).'/InlineObjectCapitalize.php';
$parser = new InlineObjectParser();
$parser->addType('caps', new InlineObjectCapitalize());

$text = 'Capitalize the next [caps:word].';
render_value($text, $parser->parse($text), 'Capitalize');


/*
 * 2) The Constant type
 */
require_once dirname(__FILE__).'/InlineObjectConstant.php';
$parser = new InlineObjectParser();
$parser->addType('const', new InlineObjectConstant());

$text = '[const:M_PI]';
render_value($text, $parser->parse($text), 'Constant');


/*
 * 3) The Date type
 */
require_once dirname(__FILE__).'/InlineObjectDate.php';
$parser = new InlineObjectParser();
$parser->addType('date', new InlineObjectDate());

$text = '[date:year]';
render_value($text, $parser->parse($text), 'Date');


/*
 * 4) The function type
 */
require_once dirname(__FILE__).'/InlineObjectFunction.php';
$parser = new InlineObjectParser();
$parser->addType('fxn', new InlineObjectFunction());

$text = '[fxn:cos arg=3.1415926]';
render_value($text, $parser->parse($text), 'Function');


/*
 * 5) The substring type
 */
require_once dirname(__FILE__).'/InlineObjectSubstring.php';
$parser = new InlineObjectParser();
$parser->addType('substring', new InlineObjectSubstring());

$text = 'Return only a "[substring:portion length=4 start=2]" of a word.';
render_value($text, $parser->parse($text), 'Substring');


/*
 * 6) The translation type
 */
require_once dirname(__FILE__).'/InlineObjectTranslate.php';
$parser = new InlineObjectParser();
$parser->addType('translate', new InlineObjectTranslate());

$text = 'I could even [translate:"translate a phrase" from=en to=es] into spanish.';
render_value($text, $parser->parse($text), 'Translate');


// Helper method for outputting in a nice way
function render_value($raw, $value, $type)
{
  echo sprintf('Testing the %s type: "%s"', $type, $raw)."\n\n";
  echo '    ' . $value ."\n\n";
}