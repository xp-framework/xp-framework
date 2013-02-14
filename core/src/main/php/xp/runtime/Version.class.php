<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Displays XP version and runtime information
   *
   * @purpose  Tool
   */
  class xp·runtime·Version extends Object {
    
    /**
     * Main
     *
     * @param   string[] args
     * @return  int
     */
    public static function main(array $args) {
      Console::writeLinef(
        'XP %s { PHP %s & ZE %s } @ %s', 
        xp::version(),
        phpversion(),
        zend_version(),
        php_uname()
      );
      Console::writeLine('Copyright (c) 2001-2013 the XP group');
      foreach (ClassLoader::getLoaders() as $delegate) {
        Console::writeLine($delegate->toString());
      }
      return 1;
    }
  }
?>
