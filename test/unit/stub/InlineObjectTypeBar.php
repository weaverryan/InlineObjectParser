<?php

class InlineObjectTypeBar extends InlineObjectType
{
  public function render($name, $options)
  {
    return $name.'_bar';
  }
}