<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.doclet';

  uses('text.doclet.RootDoc', 'util.cmd.ParamString');

  /**
   * Command line doclet runner
   * ~~~~~~~~~~~~~~~~~~~~~~~~~~
   *
   * Usage:
   * <pre>
   *   doclet class [options] name [name [name...]]
   * </pre>
   * Names can be one or more of:
   * <ul>
   *   <li>{package.name}.*: All classes inside a given package</li>
   *   <li>{package.name}.**: All classes inside a given package and all it subpackages</li>
   *   <li>{qualified.class.Name}: A fully qualified class name</li>
   * </ul> 
   *
   * @purpose  Tool
   */
  class xp·doclet·Runner extends Object {

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
     * Runner method
     *
     */
    public static function main(array $args) {
      if (!$args) self::usage();

      $class= XPClass::forName($args[0]);
      if (!$class->isSubclassOf('text.doclet.Doclet')) {
        Console::$err->writeLine('*** ', $class, ' is not a doclet');
        return -2;
      }

      RootDoc::start($class->newInstance(), new ParamString($args));
    }
  }
?>
