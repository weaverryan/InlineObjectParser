<?php

class InlineObjectTypeFoo extends InlineObjectType
{
  public function render($name, $options)
  {
    $ret = $name.'_foo';
    $ret .= isset($options['extra']) ? '_'.$options['extra'] : '';

    return $ret;
  }
}