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
   *
   * Class is the fully qualified class name of a doclet class. Options 
   * depend on the doclet implementation. 
   *
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
     * Displays usage
     *
     * @param   lang.XPClass class
     * @return  int
     */
    protected static function usage(XPClass $class) {
      Console::$err->writeLine(self::textOf($class->getComment()));
      return 1;
    }
  
    /**
     * Runner method
     *
     */
    public static function main(array $args) {
    
      // Show command usage if invoked without arguments
      if (!$args) exit(self::usage(XPClass::forName(xp::nameOf(__CLASS__))));

      $class= XPClass::forName($args[0]);
      if (!$class->isSubclassOf('text.doclet.Doclet')) {
        Console::$err->writeLine('*** ', $class, ' is not a doclet');
        exit(2);
      }
      
      // Show doclet usage if the command line contains "-?" (at any point).
      if (in_array('-?', $args)) exit(self::usage($class));

      RootDoc::start($class->newInstance(), new ParamString($args));
    }
  }
?>
