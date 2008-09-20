<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.doclet';

  uses('text.doclet.RootDoc', 'util.cmd.ParamString');

  /**
   * Command line doclet runner
   *
   * @purpose  RootDoc CLI adaptor
   */
  class text·doclet·Runner extends Object {
  
    /**
     * Runner method
     *
     */
    public static function main(array $args) {
      if (sizeof($args) < 1) {
        Console::$err->writeLine('*** No doclet classname given');
        return -1;
      }

      $class= XPClass::forName($args[0]);
      if (!$class->isSubclassOf('text.doclet.Doclet')) {
        Console::$err->writeLine('*** ', $class, ' is not a doclet');
        return -2;
      }

      RootDoc::start($class->newInstance(), new ParamString($args));
    }
  }
?>
