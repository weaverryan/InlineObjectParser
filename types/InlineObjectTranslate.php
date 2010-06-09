<?php

/**
 * Translates given text into another language
 * 
 * Usage:
 * require_once '/path/to/lib/types/InlineObjectTranslate.php';
 * 
 * $parser = new InlineObjectParser();
 * $parser->addType('translate', 'InlineObjectTranslate');
 * echo $parser->parse('I could even [translate:"translate a phrase" from=en to=es] into spanish.');
 * 
 * Original translation code taken from Doctrine's GoogleI18n template:
 * http://www.doctrine-project.org/extension/GoogleI18n
 * 
 * @package     InlineObjectParser
 * @subpackage  types
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */

class InlineObjectTranslate extends InlineObjectType
{
  protected
    $_url = 'http://ajax.googleapis.com/ajax/services/language/translate',
    $_apiVersion = '1.0';

  public function render($name, $arguments)
  {
    /*
     * Obviously, using an API to translate text without any type of cache
     * is terribly inefficient. This is here just as an example.
     */

    $from = isset($arguments['from']) ? $arguments['from'] : 'en';
    $to = isset($arguments['to']) ? $arguments['to'] : 'en';

    if ($from == $to)
    {
      return $name;
    }
    
    $langPair = $from . '|' . $to;
    $parameters = array(
      'v' => $this->_apiVersion,
      'q' => $name,
      'langpair' => $langPair
    );

    $url  = $this->_url . '?';
    foreach($parameters as $k => $p) {
      $url .= $k . '=' . urlencode($p) . '&';
    }

    $json = json_decode(file_get_contents($url));

    switch($json->responseStatus)
    {
      case 200:
        return $json->responseData->translatedText;
        break;

      default:
        throw new Exception("Unable to perform Translation:".$json->responseDetails);
    }
  }
}