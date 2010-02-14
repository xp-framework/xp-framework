<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'xp.runtime';
 
  uses('util.cmd.Console');

  /**
   * Dumps reflection information about a class
   *
   * @purpose  Tool
   */
  class xp·runtime·Reflect extends Object {

    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      try {
        $class= XPClass::forName($args[0]);
      } catch (ClassNotFoundException $e) {
        Console::$err->writeLine('*** ', $e);
        exit(1);
      }
      
      Console::writeLine('@', $class->getClassLoader());
      $mask= $class->isInterface() ? MODIFIER_ABSTRACT : 0;
      Console::write(implode(' ', Modifiers::namesOf($class->getModifiers() ^ $mask)));
      Console::write($class->isInterface() ? ' interface ' : ' class ', $class->getName());
      if ($parent= $class->getParentClass()) {
        Console::write(' extends ', $parent->getName());
      }
      if ($interfaces= $class->getInterfaces()) {
        Console::write(' implements ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $iface) {
          Console::write($iface->getName());
          $i < $s && Console::write(', ');
        }
      }
      
      // Fields
      Console::writeLine(' {');
      $i= 0;
      foreach ($class->getFields() as $field) {
        Console::writeLine('  ', $field);
        $i++;
      }
      
      // Constructor
      $i && Console::writeLine();
      $i= 0;
      if ($class->hasConstructor()) {
        Console::writeLine('  ', $class->getConstructor());
        $i++;
      }
      
      // Methods
      with ($methods= $class->getMethods()); {
        $i && Console::writeLine();
        $i= 0;
        foreach ($methods as $method) {
          if (!Modifiers::isStatic($method->getModifiers())) continue;
          Console::writeLine('  ', $method);
          $i++;
        }
        
        $i && Console::writeLine();
        $i= 0;
        foreach ($methods as $method) {
          if (Modifiers::isStatic($method->getModifiers())) continue;
          Console::writeLine('  ', $method);
          $i++;
        }
      }
      Console::writeLine('}');
    }
  }
?>
