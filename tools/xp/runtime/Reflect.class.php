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
     * Prints methods - static first, rest then
     *
     * @param   lang.reflect.Method[] methods
     */
    protected static function printMethods(array $methods) {
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
  
    /**
     * Handles enums
     *
     * @param   lang.XPClass class
     */
    protected static function printEnum(XPClass $enum) {
      Console::write(implode(' ', Modifiers::namesOf($enum->getModifiers())));
      Console::write(' enum ', $enum->getName());

      // Parent class, if not lang.Enum
      if (!XPClass::forName('lang.Enum')->equals($parent= $enum->getParentClass())) {
        Console::write(' extends ', $parent->getName());
      }

      // Interfaces
      if ($interfaces= $enum->getInterfaces()) {
        Console::write(' implements ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $iface) {
          Console::write($iface->getName());
          $i < $s && Console::write(', ');
        }
      }

      // Members
      Console::writeLine(' {');
      foreach (Enum::valuesOf($enum) as $member) {
        Console::write('  ',  $member->ordinal(), ': ', $member->name());
        $class= $member->getClass();
        if ($class->isSubclassOf($enum)) {
          Console::writeLine(' {');
          foreach ($class->getMethods() as $method) {
            if (!$class->equals($method->getDeclaringClass())) continue;
            Console::writeLine('    ', $method);
            $i++;
          }
          Console::writeLine('  }');
        } else {
          Console::writeLine();
        }
        $i++;
      }
      
      // Methods
      $i && Console::writeLine();
      self::printMethods($enum->getMethods());

      Console::writeLine('}');
    }

    /**
     * Handles interfaces
     *
     * @param   lang.XPClass class
     */
    protected static function printInterface(XPClass $iface) {
      Console::write(implode(' ', Modifiers::namesOf($iface->getModifiers() ^ MODIFIER_ABSTRACT)));
      Console::write(' interface ', $iface->getName());

      // Interfaces are this interface's parents
      if ($interfaces= $iface->getInterfaces()) {
        Console::write(' extends ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $parent) {
          Console::write($parent->getName());
          $i < $s && Console::write(', ');
        }
      }
      Console::writeLine(' {');

      $i= 0;
      if ($iface->hasConstructor()) {
        Console::writeLine('  ', $iface->getConstructor());
        $i++;
      }

      // Methods
      foreach ($iface->getMethods() as $method) {
        Console::write('  ', $method->getReturnTypeName(), ' ', $method->getName(), '(');
        if ($params= $method->getParameters()) {
          $s= sizeof($params)- 1;
          foreach ($params as $i => $param) {
            Console::write($param->getTypeName(), ' $', $param->getName());
            $i < $s && Console::write(', ');
          }
        }
        Console::write(')');
        Console::writeLine();
      }

      Console::writeLine('}');
    }

    /**
     * Handles classes
     *
     * @param   lang.XPClass class
     */
    protected static function printClass(XPClass $class) {
      Console::write(implode(' ', Modifiers::namesOf($class->getModifiers())));
      Console::write(' class ', $class->getName());

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
      $i && Console::writeLine();
      self::printMethods($class->getMethods());
      Console::writeLine('}');
    }

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
      if ($class->isInterface()) {
        self::printInterface($class);
      } else if ($class->isEnum()) {
        self::printEnum($class);
      } else {
        self::printClass($class);
      }
    }
  }
?>
