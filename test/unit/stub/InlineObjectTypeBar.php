<?php

class InlineObjectTypeBar extends InlineObjectType
{
  public function render()
  {
    return $this->getName().'_bar';
  }
}