<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.install';

  uses(
    'util.cmd.Console'
  );

  /**
   * XP Installer
   * ~~~~~~~~~~~~
   *
   * Usage:
   * <pre>
   *   # Upgrade XP Framework release
   *   $ xpi upgrade
   *   
   *   # Lists installed packages
   *   $ xpi list [pattern]
   *
   *   # Searches for available packages
   *   $ xpi search [pattern]
   *
   *   # Installs Dialog 2.4.2
   *   $ xpi install dialog-2.4.2
   *
   *   # Removes Dialog 2.4.2
   *   $ xpi remove dialog-2.4.2
   * </pre>
   *
   * @purpose  Tool
   */
  class xp·install·Runner extends Object {

    /**
     * Converts api-doc "markup" to plain text w/ ASCII "art"
     *
     * @param   string markup
     * @return  string text
     */
    protected static function textOf($markup) {
      $line= str_repeat('=', 72);
      return strip_tags(preg_replace(array(
        '#<pre>#', '#</pre>#', '#<li>#',
      ), array(
        $line, $line, '* ',
      ), trim($markup)));
    }

    /**
     * Displays usage and exits
     *
     */
    protected static function usage() {
      Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      exit(1);
    }

    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (!$args) self::usage();
    }    
  }
?>
