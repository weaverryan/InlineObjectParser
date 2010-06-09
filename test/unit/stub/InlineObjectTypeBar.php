<?php

class InlineObjectTypeBar extends InlineObjectType
{
  public function render($name, $arguments)
  {
    return $name.'_bar';
  }
}