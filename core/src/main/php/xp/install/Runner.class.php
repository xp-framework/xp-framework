<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.install';

  uses(
    'util.cmd.Console',
    'util.log.LogCategory',
    'util.log.ColoredConsoleAppender',
    'webservices.rest.RestClient'
  );

  /**
   * XP Installer
   * ============
   *
   * Basic usage
   * -----------
   * $ xpi [options] [action] [arg [arg [...]]]
   *
   * Options:
   * <ul>
   *   <li>-a: Set base for XP module registry API</li>
   *   <li>-v: Show debugging information</li>
   * </ul>
   * Actions:
   * <ul>
   *   <li>search - Search for modules</li>
   *   <li>info - Display information about a module</li>
   *   <li>add - Adds a module</li>
   *   <li>list - List installed modules</li>
   *   <li>upgrade - Upgrade an existing module</li>
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

      // Parse args
      $api= new RestClient('http://builds.planet-xp.net/');
      $action= NULL;
      $cat= NULL;
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('-?' === $args[$i] || '--help' === $args[$i]) {
          break;
        } else if ('-a' === $args[$i]) {
          $api->setBase($args[++$i]);
        } else if ('-v' === $args[$i]) {
          $cat= create(new LogCategory('console'))->withAppender(new ColoredConsoleAppender());
        } else if ('-' === $args[$i]{0}) {
          Console::$err->writeLine('*** Unknown argument ', $args[$i]);
          return 128;
        } else {
          $action= $args[$i];   // First non-option is the action name
          break;
        }
      }

      if (NULL === $action) {
        Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
        return 1;
      }

      try {
        $class= Package::forName('xp.install')->loadClass(ucfirst($action).'Action');
      } catch (ClassNotFoundException $e) {
        Console::$err->writeLine('*** No such action "'.$action.'"');
        return 2;
      }
      
      // Show help
      if (in_array('-?', $args) || in_array('--help', $args)) {
        Console::$out->writeLine(self::textOf($class->getComment()));
        return 3;
      }

      // Perform action
      $instance= $class->newInstance($api);
      $instance->setTrace($cat);
      try {
        return $instance->perform(array_slice($args, $i+ 1));
      } catch (Throwable $e) {
        Console::$err->writeLine('*** Error performing action ~ ', $e);
        return 1;
      }
    }    
  }
?>
