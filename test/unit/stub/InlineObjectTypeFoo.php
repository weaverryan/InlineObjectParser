<?php

class InlineObjectTypeFoo extends InlineObjectType
{
  public function render()
  {
    return $this->getName().'_foo';
  }
}