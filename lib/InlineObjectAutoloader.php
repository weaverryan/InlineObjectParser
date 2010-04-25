<?php

/**
 * Autoloader for the InlineObject library
 * 
 * @package     InlineObjectParser
 * @subpackage  autoload
 * @author      Ryan Weaver <ryan@thatsquality.com>
 */
class InlineObjectAutoloader
{
  protected static $_classes = array(
    'InlineObjectParser',
    'InlineObjectToolkit',
    'InlineObjectType',
  );

  /**
   * Registers InlineObjectAutoloader as an SPL autoloader.
   */
  static public function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');
    spl_autoload_register(array(new self, 'autoload'));
  }

  /**
   * Handles autoloading of classes.
   *
   * @param  string  $class  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    if (!in_array($class, self::$_classes))
    {
      return false;
    }

    require dirname(__FILE__).'/'.$class.'.php';

    return true;
  }
}