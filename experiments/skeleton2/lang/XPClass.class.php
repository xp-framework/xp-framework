<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.reflect.Field',
    'lang.reflect.Method',
    'lang.reflect.Constructor',
    'lang.InstantiationException'
  );

  define('MODIFIER_STATIC',       1);
  define('MODIFIER_ABSTRACT',     2);
  define('MODIFIER_FINAL',        4);
  define('MODIFIER_PUBLIC',     256);
  define('MODIFIER_PROTECTED',  512);
  define('MODIFIER_PRIVATE',   1024);

  /**
   * Represents classes. Every instance of an XP class has an method
   * called getClass() which returns an instance of this class.
   *
   * Warning:
   *
   * Do not construct this class publicly, instead use either the
   * $o->getClass() syntax or the static method 
   * $class= XPClass::forName('fully.qualified.Name')
   *
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= new File();
   *   echo 'The class name for $o is '.$o->getClass()->getName();
   * </code>
   *
   * @see      xp://lang.Object#getClass()
   * @purpose  Reflection
   */
  class XPClass extends Object {
    protected
      $_reflection = NULL;

    /**
     * Constructor
     *
     * @access  package
     * @param   mixed ref a class name, an object or a Reflection_Class object
     */
    public function __construct($ref) {
      if ($ref instanceof ReflectionClass) {
        $this->_reflection= $ref;
      } else {
        $this->_reflection= new ReflectionClass($ref);
      }
    }
    
    /**
     * Retrieves the fully qualified class name for this class.
     * 
     * @access  public
     * @return  string name - e.g. "io.File", "rdbms.mysql.MySQL"
     */
    public function getName() {
      return xp::nameOf($this->_reflection->getName());
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression with an empty argument list.
     *
     * Example:
     * <code>
     *   try(); {
     *     $o= XPClass::forName($name)->newInstance();
     *   } if (catch('ClassNotFoundException', $e)) {
     *     // handle it!
     *   }
     * </code>
     *
     * Example (passing arguments):
     * <code>
     *   try(); {
     *     $o= XPClass::forName('peer.Socket')->newInstance('localhost', 6100);
     *   } if (catch('ClassNotFoundException', $e)) {
     *     // handle it!
     *   }
     * </code>
     *
     * @access  public
     * @param   mixed* args
     * @return  &lang.Object
     * @throws  lang.InstantiationException
     */
    public function newInstance() {
      if (!$this->_reflection->isInstantiable()) {
        throw (new InstantiationException($this->getName().' is not instantiable'));
      }
      $args= func_get_args();
      return call_user_func_array(array($this->_reflection, 'newInstance'), $args);
    }
    
    /**
     * Gets class methods for this class
     *
     * @access  public
     * @return  lang.reflect.Method[]
     */
    public function getMethods() {
      $m= array();
      foreach ($this->_reflection->getMethods() as $method) {
        $m[]= new Method($method);
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
     * @see     xp://lang.reflect.Method
     */
    public function getMethod($name) {
      if (!($m= $this->_reflection->getMethod($name))) return xp::$null;
      return new Method($m); 
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
    public function hasMethod($method) {
      return (NULL !== $this->_reflection->getMethod($method));
    }
    
    /**
     * Retrieves this class' constructor
     *
     * @access  public
     * @return  &lang.reflect.Constructor
     * @see     xp://lang.reflect.Constructor
     */
    public function getConstructor() {
      if (!($c= $this->_reflection->getConstructor())) return xp::$null;
      return new Constructor($c); 
    }
    
    /**
     * Retrieve a list of all declared member variables
     *
     * @access  public
     * @return  lang.reflect.Field[]
     */
    public function getFields() {
      $f= array();
      foreach ($this->_reflection->getProperties() as $prop) {
        $f[]= new Field($prop);
      }
      return $f;
    }
    
    /**
     * Retrieve the parent class's class object. Returns NULL if there
     * is no parent class.
     *
     * @access  public
     * @return  &lang.XPClass class object
     */
    public function getParentclass() {
      if (!($p= $this->_reflection->getParentClass())) return xp::$null;
      return new XPClass($p);
    }
    
    /**
     * Tests whether this class is a subclass of a specified class.
     *
     * @access  public
     * @param   string name class name
     * @return  bool
     */
    public function isSubclassOf($name) {
      return $this->_reflection->isSubclassOf(new Reflection_Class(xp::reflect($name)));
    }
    
    /**
     * Determines whether the specified object is an instance of this
     * class. This is the equivalent of the is() core functionality.
     *
     * <code>
     *   uses('io.File', 'io.TempFile');
     *   $class= XPClass::forName('io.File');
     * 
     *   var_dump($class->isInstance(new TempFile()));  // TRUE
     *   var_dump($class->isInstance(new File()));      // TRUE
     *   var_dump($class->isInstance(new Object()));    // FALSE
     * </code>
     *
     * @access  public
     * @param   &lang.Object obj
     * @return  bool
     */
    public function isInstance(Object $obj) {
      return $this->_reflection->isInstance($obj);
    }
    
    /**
     * Determines if this XPClass object represents an interface type.
     *
     * @access  public
     * @return  bool
     */
    public function isInterface() {
      return $this->_reflection->isInterface();
    }
    
    /**
     * Retrieve interfaces this class implements
     *
     * @access  public
     * @return  lang.XPClass[]
     */
    public function getInterfaces() {
      $r= array();
      foreach ($this->_reflection->getInterfaces() as $iface) {
        $r[]= new XPClass($iface);
      }
      return $r;
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
    public static function forName($name, $classloader= NULL) {
      if (NULL === $classloader) {
        $classloader= ClassLoader::getDefault();
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
    public static function getClasses() {
      $r= array();
      foreach (get_declared_classes() as $name) {
        if (isset(xp::$classes[strtolower($name)])) $r[]= new XPClass($name);
      }
      return $r;
    }
  }
?>
