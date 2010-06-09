<?php

class InlineObjectTypeFoo extends InlineObjectType
{
  public function render($name, $arguments)
  {
    $ret = $name.'_foo';
    $ret .= isset($arguments['extra']) ? '_'.$arguments['extra'] : '';

    return $ret;
  }
}