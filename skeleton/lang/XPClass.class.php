<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Method');
 
  /**
   * Represents classes. Every instance of an XP class has an method
   * called getClass() which returns an instance of this class.
   *
   * Warning:
   *
   * Do not construct this class publicly, instead use either the
   * $o->getClass() syntax or the static method 
   * $class= &XPClass::forName('fully.qualified.Name')
   *
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= &new File();
   *   $c= &$o->getClass();
   *   echo 'The class name for $o is '.$c->getName();
   * </code>
   *
   * @see      xp://lang.Object#getClass()
   * @purpose  Reflection
   */
  class XPClass extends Object {
    var 
      $_objref  = NULL,
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  package
     * @param   &mixed ref either a class name or an object
     */
    function __construct(&$ref) {
      parent::__construct();
      $this->_objref= &$ref;
      $this->name= xp::nameOf(is_object($ref) ? get_class($ref) : $ref);
    }
    
    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * @access  public
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression with an empty argument list.
     *
     * Example:
     * <code>
     *   try(); {
     *     $c= &XPClass::forName($name) &&
     *     $o= &$c->newInstance();
     *   } if (catch('ClassNotFoundException', $e)) {
     *     // handle it!
     *   }
     * </code>
     *
     * Example (passing arguments):
     * <code>
     *   try(); {
     *     $c= &XPClass::forName('peer.Socket') &&
     *     $o= &$c->newInstance('localhost', 6100);
     *   } if (catch('ClassNotFoundException', $e)) {
     *     // handle it!
     *   }
     * </code>
     *
     * @access  public
     * @param   mixed* args
     * @return  &lang.Object 
     */
    function &newInstance() {
      $paramstr= '';
      $args= func_get_args();
      for ($i= 0, $m= func_num_args(); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      return eval('return new '.xp::reflect($this->name).'('.substr($paramstr, 2).');');
    }
    
    /**
     * Gets class methods for this class
     *
     * @access  public
     * @return  lang.Method[]
     */
    function getMethods() {
      $m= array();
      foreach (get_class_methods($this->_objref) as $method) {
        $m[]= &new Method($this->_objref, $method);
      }
      return $m;
    }

    /**
     * Gets a method by a specified name. Returns NULL if the specified 
     * method does not exist.
     *
     * @access  public
     * @param   string name
     * @return  &lang.Method
     * @see     xp://lang.Method
     */
    function &getMethod($name) {
      if (!$this->hasMethod($name)) return NULL;

      return new Method($this->_objref, $name); 
    }
    
    /**
     * Checks whether this class has a method named "$method" or not.
     *
     * Note: Since in PHP, methods are case-insensitive, calling 
     * hasMethod('toString') will provide the same result as 
     * hasMethod('tostring')
     *
     * @access  public
     * @param   string method the method's name
     * @return  bool TRUE if method exists
     */
    function hasMethod($method) {
      return in_array(
        strtolower($method),
        get_class_methods($this->_objref)
      );
    }
    
    /**
     * Retrieve a list of all declared member variables
     *
     * @access  public
     * @return  string[] member names
     */
    function getFields() {
      return (is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
    }
    
    /**
     * Retrieve the parent class's class object
     *
     * @access  public
     * @return  &lang.XPClass class object
     */
    function &getParentclass() {
      return new XPClass(get_parent_class($this->_objref));
    }
    
    /**
     * Tests whether this class is a subclass of a specified class.
     *
     * @access  public
     * @param   string name class name
     * @return  bool
     */
    function isSubclassOf($name) {
      $cmp= xp::reflect($this->name);
      $name= xp::reflect($name);
      while ($cmp= get_parent_class($cmp)) {
        if ($cmp == $name) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Returns the XPClass object associated with the class with the given 
     * string name. Uses the default classloader if none is specified.
     *
     * @model   static
     * @access  public
     * @param   string name - e.g. "io.File", "rdbms.mysql.MySQL"
     * @param   lang.ClassLoader classloader default NULL
     * @return  &lang.XPClass class object
     * @throws  lang.ClassNotFoundException when there is no such class
     */
    function &forName($name, $classloader= NULL) {
      if (NULL === $classloader) {
        $classloader= &ClassLoader::getDefault();
      }
    
      return $classloader->loadClass($name);
    }
    
    /**
     * Returns an array containing class objects representing all the 
     * public classes
     *
     * @model   static
     * @access  public
     * @return  &lang.XPClass[] class objects
     */
    function getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        if (xp::registry('class.'.$name)) $ret[]= &new XPClass($name);
      }
      return $ret;
    }
  }
?>
