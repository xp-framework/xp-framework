<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.IClassLoader');
  
  /** 
   * Loads a class from the filesystem
   * 
   * @test  xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @see   xp://lang.XPClass#forName
   */
  abstract class AbstractClassLoader extends Object implements IClassLoader {
    public $path= '';
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->loadClass0($class));
    }
    
    /**
     * Returns URI suitable for include() given a class name
     *
     * @param   string class
     * @return  string
     */
    protected abstract function classUri($class);

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     * @throws  lang.ClassFormatException in case the class format is invalud
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) return xp::reflect($class);

      // Load class
      $package= NULL;
      xp::$registry['classloader.'.$class]= $this->getClassName().'://'.$this->path;
      xp::$registry['cl.level']++;
      try {
        $r= include($this->classUri($class));
      } catch (ClassLoadingException $e) {
        xp::$registry['cl.level']--;

        $decl= (NULL === $package
          ? substr($class, (FALSE === ($p= strrpos($class, '.')) ? 0 : $p + 1))
          : strtr($class, '.', '·')
        );

        // If class was declared, but loading threw an exception it means
        // a "soft" dependency, one that is only required at runtime, was
        // not loaded, the class itself has been declared.
        if (class_exists($decl, FALSE) || interface_exists($decl, FALSE)) {
          raise('lang.ClassDependencyException', $class, array($this), $e);
        }

        // If otherwise, a "hard" dependency could not be loaded, eg. the
        // base class or a required interface and thus the class could not
        // be declared.
        raise('lang.ClassLinkageException', $class, array($this), $e);
      }
      xp::$registry['cl.level']--;
      if (FALSE === $r) {
        unset(xp::$registry['classloader.'.$class]);
        throw new ClassNotFoundException($class, array($this));
      }
      
      // Register it
      $name= ($package ? strtr($package, '.', '·').'·' : '').substr($class, (FALSE === ($p= strrpos($class, '.')) ? 0 : $p + 1));
      if (!class_exists($name, FALSE) && !interface_exists($name, FALSE)) {
        unset(xp::$registry['classloader.'.$class]);
        raise('lang.ClassFormatException', 'Class "'.$name.'" not declared in loaded file');
      }
      xp::$registry['class.'.$name]= $class;
      method_exists($name, '__static') && xp::$registry['cl.inv'][]= array($name, '__static');
      if (0 == xp::$registry['cl.level']) {
        $invocations= xp::$registry['cl.inv'];
        xp::$registry['cl.inv']= array();
        foreach ($invocations as $inv) call_user_func($inv);
      }
      return $name;
    }

    /**
     * Checks whether two class loaders are equal
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->path === $this->path;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName(). '<'.$this->path.'>';
    }
  }
?>
