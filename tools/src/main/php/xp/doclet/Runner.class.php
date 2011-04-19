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
   *   doclet [options] class [doclet-options] name [name [name...]]
   * </pre>
   *
   * Class is the fully qualified class name of a doclet class.
   *
   * Options can be one or more of:
   * <ul>
   *   <li>-sp sourcepath: Sets sourcepath - paths in which the doclet
   *     implementation will search for classes. Multiple paths are
   *     separated by the path separator char.</li>
   *   <li>-cp classpath: Adds classpath element in which the class
   *     loader will search for the doclet class.</li>
   * </ul>
   * Doclet-Options depend on the doclet implementation. 
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
      
      $root= new RootDoc();
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('-sp' === $args[$i]) {
          $root->setSourcePath(explode(PATH_SEPARATOR, $args[++$i]));
        } else if ('-cp' === $args[$i]) {
          ClassLoader::registerPath($args[++$i]);
        } else {
          try {
            $class= XPClass::forName($args[$i]);
          } catch (ClassNotFoundException $e) {
            Console::$err->writeLine('*** ', $e->getMessage());
            exit(2);
          }
          if (!$class->isSubclassOf('text.doclet.Doclet')) {
            Console::$err->writeLine('*** ', $class, ' is not a doclet');
            exit(2);
          }
          $doclet= $class->newInstance();
          $params= new ParamString(array_slice($args, $i));
      
          // Show doclet usage if the command line contains "-?" (at any point).
          if ($params->exists('help', '?')) {
            self::usage($class);
            if ($valid= $doclet->validOptions()) {
              Console::$err->writeLine();
              Console::$err->writeLine('Options:');
              foreach ($valid as $name => $value) {
                Console::$err->writeLine('  * --', $name, OPTION_ONLY == $value ? '' : '=<value>');
              }
            }
            exit(3);
          }

          $root->start($doclet, $params);
          exit(0);
        }
      }
      Console::$err->writeLine('*** No doclet classname given');
      exit(1);
    }
  }
?>
