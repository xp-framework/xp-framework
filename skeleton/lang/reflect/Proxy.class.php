<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('PROXY_PREFIX',    'Proxy·');

  /**
   * Proxy provides static methods for creating dynamic proxy
   * classes and instances, and it is also the superclass of all
   * dynamic proxy classes created by those methods.
   *
   * @purpose  Dynamically create classes
   * @see      http://java.sun.com/j2se/1.4.2/docs/api/java/lang/reflect/Proxy.html
   */
  class Proxy extends Object {
    var
      $_h= NULL;
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   &lang.reflect.InvocationHandler handler
     */
    function __construct(&$handler) {
      $this->_h= &$handler;
    }
  
    /**
     * Returns the XPClass object for a proxy class given a class loader 
     * and an array of interfaces.  The proxy class will be defined by the 
     * specified class loader and will implement all of the supplied 
     * interfaces (also loaded by the classloader).
     *
     * @model   static
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string[] interfaces names of the interfaces to implement
     * @return  &lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    function &getProxyClass(&$classloader, $interfaces) {
      static $num= 0;
      static $cache= array();
      
      // Calculate cache key (composed of the names of all interfaces)
      $key= $classloader->hashCode().':'.implode(';', $interfaces);
      if (isset($cache[$key])) return $cache[$key];
      
      // Create proxy class' name, using a unique identifier and a prefix
      $name= PROXY_PREFIX.($num++);
      $implements= xp::registry('implements');
      $bytes= 'class '.$name.' extends Proxy { ';

      foreach ($interfaces as $interface) {
        try(); {
          $if= &XPClass::forName($interface, $classloader);
        } if (catch('ClassNotFoundException', $e)) {
          return throw(new IllegalArgumentException($e->getMessage()));
        }
        
        // Verify that the Class object actually represents an interface
        if (!$if->isInterface()) {
          return throw(new IllegalArgumentException($if->getName().' is not an interface'));
        }
        
        // Implement all the interface's methods
        for ($i= 0, $methods= $if->getMethods(), $s= sizeof($methods); $i < $s; $i++) {

          // Build signature and argument list
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
            'return $this->_h->invoke($this, \''.$methods[$i]->getName().'\', array('.$args.')); '.
            '}'
          );
        }
        
        // Implement the interface itself
        $implements[strtolower($name)][xp::reflect($interface)]= 1;
      }
      $bytes.= ' }';

      // Define the generated class
      try(); {
        $class= &$classloader->defineClass($name, $bytes);
      } if (catch('FormatException', $e)) {
        return throw(new IllegalArgumentException($e->getMessage()));
      }

      // Register implemented interfaces
      xp::registry('implements', $implements);
      
      // Update cache and return XPClass object
      $cache[$key]= &$class;
      return $class;
    }
  
    /**
     * Returns an instance of a proxy class for the specified interfaces
     * that dispatches method invocations to the specified invocation
     * handler.
     *
     * @model   static
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string[] interfaces
     * @param   &lang.reflect.InvocationHandler handler
     * @return  &lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    function &newProxyInstance(&$classloader, $interfaces, &$handler) {
      if (!($class= &Proxy::getProxyClass($classloader, $interfaces))) return $class;
      return $class->newInstance($handler);
    }
  }
?>
