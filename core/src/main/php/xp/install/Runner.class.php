<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.install';

  uses('util.cmd.Console');

  /**
   * XP Installer
   * ~~~~~~~~~~~~
   *
   * Basic usage:
   * <pre>
   * # This will install the newest release of the specified module
   * $ xpi add vendor/module
   *
   * # This will install a specific version
   * $ xpi add vendor/module 1.0.0
   *
   * # This will remove the module in the given version
   * $ xpi remove vendor/module@1.0.0
   *
   * # This will list installed modules
   * $ xpi list
   * </pre>
   *
   * Using development versions
   * <pre>
   * # This will install the master branch of the specified module from GitHub
   * $ xpi add vendor/module :master
   * </pre>
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
      
      try {
        $class= Package::forName('xp.install')->loadClass(ucfirst($args[0]).'Action');
      } catch (ClassNotFoundException $e) {
        Console::$err->writeLine('*** No such action '.$args[0].': ', $e);
        exit(2);
      }
      
      // Show help
      if (in_array('-?', $args)) {
        Console::$out->writeLine(self::textOf($class->getComment()));
        exit(3);
      }
      
      // Perform action
      try {
        $class->newInstance()->perform(array_slice($args, 1));
      } catch (Throwable $e) {
        Console::$err->writeLine('*** Error performing action ~ ', $e);
        exit(1);
      }
    }    
  }
?>
