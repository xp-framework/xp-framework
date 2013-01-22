<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.scriptlet';

  /**
   * Scriptlet usage
   *
   */
  class xp·scriptlet·Usage extends Object {

    /**
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      Console::$err->writeLine(XPClass::forName(xp::nameOf(__CLASS__))->getPackage()->getResource($args[0]));
      exit(0xFF);
    }
  }
?>
