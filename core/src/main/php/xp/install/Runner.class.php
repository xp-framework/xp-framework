<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.install';

  uses('util.cmd.Console');

  /**
   * XP Installer
   * ============
   *
   * Basic usage
   * -----------
   * $ xpi [action] [option [option [...]]]
   *
   * Actions:
   * <ul>
   *   <li>search - Search for modules</li>
   *   <li>add - Adds a module</li>
   *   <li>list - List installed modules</li>
   *   <li>remove - Removes installed module</li>
   * </ul>
   * Options
   * -------
   * All commands support "-?" to show their usage.
   *
   * @see  https://github.com/xp-framework/xp-framework/pull/287
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
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (empty($args) || '-?' === $args[0] || '--help' === $args[0]) {
        Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
        return 1;
      }

      try {
        $class= Package::forName('xp.install')->loadClass(ucfirst($args[0]).'Action');
      } catch (ClassNotFoundException $e) {
        Console::$err->writeLine('*** No such action '.$args[0].': ', $e);
        return 2;
      }
      
      // Show help
      if (in_array('-?', $args) || in_array('--help', $args)) {
        Console::$out->writeLine(self::textOf($class->getComment()));
        return 3;
      }
      
      // Perform action
      try {
        return $class->newInstance()->perform(array_slice($args, 1));
      } catch (Throwable $e) {
        Console::$err->writeLine('*** Error performing action ~ ', $e);
        return 1;
      }
    }    
  }
?>
