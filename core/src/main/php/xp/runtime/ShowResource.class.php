<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Shows a given resource from the package this class is contained in
   * on standard error and exit with a given value.
   *
   * Example:
   * <pre>
   *   $ xp ShowResource usage.txt 255
   * </pre>
   *
   * @purpose  Tool
   */
  class xp·runtime·ShowResource extends Object {

    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      Console::$err->writeLine(XPClass::forName(xp::nameOf(__CLASS__))->getPackage()->getResource($args[0]));
      exit((int)$args[1]);
    }
  }
?>
