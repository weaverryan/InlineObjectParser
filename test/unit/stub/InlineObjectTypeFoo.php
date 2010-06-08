<?php

class InlineObjectTypeFoo extends InlineObjectType
{
  public function render($name, $options)
  {
    return $name.'_foo';
  }
}