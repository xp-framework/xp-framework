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
     * Gets class name (and generic components if this class is a 
     * generic definition)
     *
     * @param   lang.XPClass class
     * @return  string
     */
    protected static function displayNameOf(XPClass $class) {
      return $class->getName().($class->isGenericDefinition()
        ? '<'.implode(', ', $class->genericComponents()).'>'
        : ''
      );
    }
  
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
      Console::write(' enum ', self::displayNameOf($enum));

      // Parent class, if not lang.Enum
      if (!XPClass::forName('lang.Enum')->equals($parent= $enum->getParentClass())) {
        Console::write(' extends ', self::displayNameOf($parent));
      }

      // Interfaces
      if ($interfaces= $enum->getInterfaces()) {
        Console::write(' implements ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $iface) {
          Console::write(self::displayNameOf($iface));
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
          foreach ($class->getDeclaredMethods() as $method) {
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
      Console::write(' interface ', self::displayNameOf($iface));

      // Interfaces are this interface's parents
      if ($interfaces= $iface->getDeclaredInterfaces()) {
        Console::write(' extends ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $parent) {
          Console::write(self::displayNameOf($parent));
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
      Console::write(' class ', self::displayNameOf($class));
      
      if ($parent= $class->getParentClass()) {
        Console::write(' extends ', self::displayNameOf($parent));
      }
      if ($interfaces= $class->getDeclaredInterfaces()) {
        Console::write(' implements ');
        $s= sizeof($interfaces)- 1;
        foreach ($interfaces as $i => $iface) {
          Console::write(self::displayNameOf($iface));
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
     * Prints package
     *
     * @param   lang.reflect.Package package
     */
    protected static function printPackage($package) {
      Console::writeLine('package ', $package->getName(), ' {');

      // Child packages
      foreach ($package->getPackages() as $child) {
        Console::writeLine('  package ', $child->getName());
      }
      
      // Classes
      $order= array(
        'interface' => array(),
        'enum'      => array(),
        'class'     => array()
      );
      foreach ($package->getClasses() as $class) {
        $mod= $class->getModifiers();
        if ($class->isInterface()) {
          $type= 'interface';
          $mod= $mod ^ MODIFIER_ABSTRACT;
        } else if ($class->isEnum()) {
          $type= 'enum';
        } else {
          $type= 'class';
        }
        
        $name= self::displayNameOf($class);
        $order[$type][]= implode(' ', Modifiers::namesOf($mod)).' '.$type.' '.self::displayNameOf($class);
      }
      foreach ($order as $type => $classes) {
        if (empty($classes)) continue;

        Console::writeLine();
        sort($classes);
        foreach ($classes as $name) {
          Console::writeLine('  ', $name);
        }
      }

      Console::writeLine('}');
    }

    /**
     * Main
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (sizeof($args) < 1 || '' == $args[0]) {
        Console::$err->writeLine('*** No class or package name given');
        return 2;
      }
      
      // Check whether a class or a package is given
      $cl= ClassLoader::getDefault();
      if ($cl->providesClass($args[0])) {
        $class= XPClass::forName($args[0], $cl);
        Console::writeLine('@', $class->getClassLoader());
        if ($class->isInterface()) {
          self::printInterface($class);
        } else if ($class->isEnum()) {
          self::printEnum($class);
        } else {
          self::printClass($class);
        }
        return 0;
      }
     
      // Not a class, check for packages
      $provided= FALSE;
      foreach ($cl->getLoaders() as $loader) {
        if (!$loader->providesPackage($args[0])) continue;
        Console::writeLine('@', $loader);
        $provided= TRUE;
      }
      
      if ($provided) {
        self::printPackage(Package::forName($args[0]));
        return 0;
      }
      
      // Not found
      Console::$err->writeLine('*** Failed to locate either a class or a package named "', $args[0], '", tried all of {');
      foreach ($cl->getLoaders() as $loader) {
        Console::$err->writeLine('  ', $loader);
      }
      Console::$err->writeLine('}');
      return 1;
    }
  }
?>
