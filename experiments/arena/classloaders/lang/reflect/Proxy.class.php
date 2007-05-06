<?php
/* This class is part of the XP framework
 *
 * $Id: Proxy.class.php 9090 2007-01-03 13:57:55Z friebe $ 
 */

  define('PROXY_PREFIX',    'Proxy·');

  /**
   * Proxy provides static methods for creating dynamic proxy
   * classes and instances, and it is also the superclass of all
   * dynamic proxy classes created by those methods.
   *
   * @test     xp://net.xp_framework.unittest.reflection.ProxyTest
   * @purpose  Dynamically create classes
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/lang/reflect/Proxy.html
   */
  class Proxy extends Object {
    public
      $_h= NULL;

    /**
     * Constructor
     *
     * @param   lang.reflect.InvocationHandler handler
     */
    public function __construct($handler) {
      $this->_h= $handler;
    }
    
    /**
     * Returns the XPClass object for a proxy class given a class loader 
     * and an array of interfaces.  The proxy class will be defined by the 
     * specified class loader and will implement all of the supplied 
     * interfaces (also loaded by the classloader).
     *
     * @param   lang.ClassLoader classloader
     * @param   lang.XPClass[] interfaces names of the interfaces to implement
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function getProxyClass($classloader, $interfaces) {
      static $num= 0;
      static $cache= array();
      
      // Calculate cache key (composed of the names of all interfaces)
      $key= $classloader->hashCode().':'.implode(';', array_map(
        create_function('$i', 'return $i->getName();'), 
        $interfaces
      ));
      if (isset($cache[$key])) return $cache[$key];
      
      // Create proxy class' name, using a unique identifier and a prefix
      $name= PROXY_PREFIX.($num++);
      $bytes= 'class '.$name.' extends Proxy implements ';
      $added= array();
      
      for ($j= 0, $t= sizeof($interfaces); $j < $t; $j++) {
        $bytes.= xp::reflect($interfaces[$j]->getName()).', ';
      }
      $bytes= substr($bytes, 0, -2)." {\n";

      for ($j= 0, $t= sizeof($interfaces); $j < $t; $j++) {
        $if= $interfaces[$j];
        
        // Verify that the Class object actually represents an interface
        if (!$if->isInterface()) {
          throw(new IllegalArgumentException($if->getName().' is not an interface'));
        }
        
        // Implement all the interface's methods
        for ($i= 0, $methods= $if->getMethods(), $s= sizeof($methods); $i < $s; $i++) {
        
          // Check for already declared methods, do not redeclare them
          if (isset($added[$methods[$i]->getName()])) continue;
          $added[$methods[$i]->getName()]= TRUE;

          // Build signature and argument list
          if ($methods[$i]->hasAnnotation('overloaded')) {
            $signatures= $methods[$i]->getAnnotation('overloaded', 'signatures');
            $max= 0;
            $cases= array();
            foreach ($signatures as $signature) {
              $args= sizeof($signature);
              $max= max($max, $args- 1);
              if (isset($cases[$args])) continue;
              
              $cases[$args]= (
                'case '.$args.': '.
                'return $this->_h->invoke($this, \''.$methods[$i]->getName(TRUE).'\', array('.
                ($args ? '$_'.implode(', $_', range(0, $args- 1)) : '').'));'
              );
            }

            // Create method
            $bytes.= (
              'function '.$methods[$i]->getName().'($_'.implode('= NULL, $_', range(0, $max)).'= NULL) { '.
              'switch (func_num_args()) {'.implode("\n", $cases).
              ' default: throw new IllegalArgumentException(\'Illegal number of arguments\'); }'.
              '}'."\n"
            );
          } else {
            $signature= $args= '';
            foreach ($methods[$i]->getArguments() as $argument) {
              $signature.= ', $'.$argument->getName();
              $args.= ', $'.$argument->getName();
              $argument->isOptional() && $signature.= '= '.$argument->getDefault();
            }
            $signature= substr($signature, 2);
            $args= substr($args, 2);

            // Create method
            $bytes.= (
              'function '.$methods[$i]->getName().'('.$signature.') { '.
              'return $this->_h->invoke($this, \''.$methods[$i]->getName(TRUE).'\', array('.$args.')); '.
              '}'."\n"
            );
          }
        }
      }
      $bytes.= ' }';

      // Define the generated class
      try {
        $dyn= DynamicClassLoader::instanceFor(__METHOD__);
        $dyn->setClassBytes($name, $bytes);
        $class= $dyn->loadClass($name);
      } catch (FormatException $e) {
        throw(new IllegalArgumentException($e->getMessage()));
      }

      // Update cache and return XPClass object
      $cache[$key]= $class;
      return $class;
    }
  
    /**
     * Returns an instance of a proxy class for the specified interfaces
     * that dispatches method invocations to the specified invocation
     * handler.
     *
     * @param   lang.ClassLoader classloader
     * @param   lang.XPClass[] interfaces
     * @param   lang.reflect.InvocationHandler handler
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public static function newProxyInstance($classloader, $interfaces, $handler) {
      return self::getProxyClass($classloader, $interfaces)->newInstance($handler);
    }
  }
?>
