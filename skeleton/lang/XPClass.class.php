<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.reflect.Method',
    'lang.reflect.Field',
    'lang.reflect.Constructor'
  );

  define('MODIFIER_STATIC',       1);
  define('MODIFIER_ABSTRACT',     2);
  define('MODIFIER_FINAL',        4);
  define('MODIFIER_PUBLIC',     256);
  define('MODIFIER_PROTECTED',  512);
  define('MODIFIER_PRIVATE',   1024);
  
  define('DETAIL_MODIFIERS',      0);
  define('DETAIL_ARGUMENTS',      1);
  define('DETAIL_RETURNS',        2);
  define('DETAIL_THROWS',         3);
  define('DETAIL_COMMENT',        4);
  define('DETAIL_ANNOTATIONS',    5);
  define('DETAIL_NAME',           6);
 
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
   * @test     xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @test     xp://net.xp_framework.unittest.reflection.ClassDetailsTest
   * @purpose  Reflection
   */
  class XPClass extends Object {
    var 
      $_objref  = NULL,
      $name     = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed ref either a class name or an object
     */
    function __construct(&$ref) {
      $this->_objref= &$ref;
      $this->name= xp::nameOf(is_object($ref) ? get_class($ref) : $ref);
    }

    /**
     * Return whether an object equals this class
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    function equals(&$cmp) {
      return (is_a($cmp, 'XPClass') 
        ? 0 == strcmp($this->getName(), $cmp->getName())
        : FALSE
      );
    }

    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return $this->name;
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return 'lang.XPClass<'.$this->name.'>';
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
      for ($args= func_get_args(), $paramstr= '', $i= 0, $m= sizeof($args); $i < $m; $i++) {
        $paramstr.= ', $args['.$i.']';
      }
      
      eval('$instance= &new '.xp::reflect($this->name).'('.substr($paramstr, 2).');');
      return $instance;
    }
    
    /**
     * Helper function that returns this class' methods, excluding the
     * constructor (and inherited constructors) and the destructor.
     *
     * @access  private
     * @return  string[] method names
     */
    function _methods() {
      $methods= array_flip(get_class_methods($this->_objref));
      
      // Well-known methods
      unset($methods['__construct']);
      unset($methods['__destruct']);

      // "Inherited" constructors
      $c= is_object($this->_objref) ? get_class($this->_objref) : $this->_objref;
      do {
        unset($methods[$c]);
      } while ($c= get_parent_class($c));

      return array_keys($methods);
    }
    
    /**
     * Gets class methods for this class
     *
     * @access  public
     * @return  lang.reflect.Method[]
     */
    function getMethods() {
      $m= array();
      foreach ($this->_methods() as $method) {
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
     * @see     xp://lang.reflect.Method
     */
    function &getMethod($name) {
      if (!$this->hasMethod($name)) return NULL;

      $m= &new Method($this->_objref, $name); 
      return $m;
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
      return in_array(strtolower($method), $this->_methods());
    }
    
    /**
     * Retrieve if a constructor exists
     *
     * @access  public
     * @return  bool
     */
    function hasConstructor() {
      return in_array('__construct', get_class_methods($this->_objref));
    }
    
    /**
     * Retrieves this class' constructor. Returns NULL if no constructor
     * exists.
     *
     * @access  public
     * @return  &lang.reflect.Constructor
     * @see     xp://lang.reflect.Constructor
     */
    function &getConstructor() {
      if ($this->hasConstructor()) {
        return new Constructor($this->_objref); 
      }
      return NULL;
    }
    
    /**
     * Retrieve a list of all member variables
     *
     * @access  public
     * @return  lang.reflect.Field[] array of field objects
     */
    function getFields() {
      $f= array();
      foreach ((is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      ) as $field => $value) {
        if ('__id' == $field) continue;
        $f[]= &new Field($this->_objref, $field, isset($value) ? gettype($value) : NULL);
      }
      return $f;
    }
    
    /**
     * Retrieve a field by a specified name. Returns NULL if the specified
     * field does not exist
     *
     * @access  public
     * @param   string name
     * @return  &lang.reflect.Field
     */
    function &getField($name) {
      if (!$this->hasField($name)) return NULL;

      $v= (is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
      return new Field($this->_objref, $name, isset($v[$name]) ? gettype($v[$name]) : NULL);
    }
    
    /**
     * Checks whether this class has a field named "$field" or not.
     *
     * @access  public
     * @param   string field the fields's name
     * @return  bool TRUE if field exists
     */
    function hasField($field) {
      return '__id' == $field ? FALSE : array_key_exists($field, is_object($this->_objref) 
        ? get_object_vars($this->_objref) 
        : get_class_vars($this->_objref)
      );
    }

    /**
     * Retrieve the parent class's class object. Returns NULL if there
     * is no parent class.
     *
     * @access  public
     * @return  &lang.XPClass class object
     */
    function &getParentclass() {
      if (!($p= get_parent_class($this->_objref))) return NULL;
      return new XPClass($p);
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
     * Determines whether the specified object is an instance of this
     * class. This is the equivalent of the is() core functionality.
     *
     * <code>
     *   uses('io.File', 'io.TempFile');
     *   $class= &XPClass::forName('io.File');
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
    function isInstance(&$obj) {
      return is($this->name, $obj);
    }
    
    /**
     * Determines if this XPClass object represents an interface type.
     *
     * @access  public
     * @return  bool
     */
    function isInterface() {
      return $this->isSubclassOf('lang.Interface');
    }
    
    /**
     * Retrieve interfaces this class implements
     *
     * @access  public
     * @return  lang.XPClass[]
     */
    function getInterfaces() {
      $r= array();
      $c= xp::reflect($this->name);
      $implements= xp::registry('implements');
      if (isset($implements[$c])) foreach (array_keys($implements[$c]) as $iface) {
        $r[]= &new XPClass($iface);
      }
      return $r;
    }

    /**
     * Check whether an annotation exists
     *
     * @access  public
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForClass($this->name);

      return $details && ($key 
        ? array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      );
    }

    /**
     * Retrieve annotation by name
     *
     * @access  public
     * @param   string name
     * @param   string key default NULL
     * @return  mixed
     * @throws  lang.ElementNotFoundException
     */
    function getAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForClass($this->name);

      if (!$details || !($key 
        ? array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      )) return raise(
        'lang.ElementNotFoundException', 
        'Annotation "'.$name.($key ? '.'.$key : '').'" does not exist'
      );

      return ($key 
        ? $details['class'][DETAIL_ANNOTATIONS][$name][$key] 
        : $details['class'][DETAIL_ANNOTATIONS][$name]
      );
    }

    /**
     * Retrieve whether a method has annotations
     *
     * @access  public
     * @return  bool
     */
    function hasAnnotations() {
      $details= XPClass::detailsForClass($this->name);
      return $details ? !empty($details['class'][DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of a method's annotations
     *
     * @access  public
     * @return  array annotations
     */
    function getAnnotations() {
      $details= XPClass::detailsForClass($this->name);
      return $details ? $details['class'][DETAIL_ANNOTATIONS] : array();
    }
    
    /**
     * Retrieve the class loader a class was loaded with
     *
     * @access  public
     * @return  &lang.ClassLoader
     */
    function &getClassLoader() {
      return XPClass::_classLoaderFor($this->name);
    }
    
    /**
     * Fetch a class' classloader by its name
     *
     * @model   static
     * @access  protected
     * @param   string name fqcn of class
     * @return  &lang.ClassLoader
     */
    function &_classLoaderFor($name) {
      if (!($cl= &xp::registry('classloader.'.$name))) {
        return ClassLoader::getDefault();
      }

      // The class loader information can be a string identifying the responsible
      // classloader for the class. In that case, fetch it's class and get an
      // instance through the instanceFor() method.
      if (is_string($cl)) {
        list($className, $argument)= sscanf($cl, '%[^:]://%s');
        $class= &XPClass::forName($className);
        $method= &$class->getMethod('instanceFor');

        $dummy= NULL;
        $cl= &$method->invoke($dummy, array($argument));
        
        // Replace the "symbolic" representation of the classloader with a reference
        // to an instance.
        xp::registry('classloader.'.$name, $cl);
      }
      
      return $cl;
    }
    
    /**
     * Retrieve details for a specified class. Note: Results from this 
     * method are cached!
     *
     * @model   static
     * @access  public
     * @param   string class fully qualified class name
     * @return  array or NULL to indicate no details are available
     */
    function detailsForClass($class) {
      static $details= array();

      if (!$class) return NULL;        // Border case
      if (!isset($details[$class])) {
        $cl= &XPClass::_classLoaderFor($class);
        $details[$class]= $cl->getClassDetails($class);
      }
      return $details[$class];
    }

    /**
     * Retrieve details for a specified class and method. Note: Results 
     * from this method are cached!
     *
     * @model   static
     * @access  public
     * @param   string class unqualified class name
     * @param   string method
     * @return  array
     */
    function detailsForMethod($class, $method) {
      $method= strtolower($method);
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[1][$method])) return $details[1][$method];
        $class= get_parent_class($class);
      }
      return NULL;
    }

    /**
     * Retrieve details for a specified class and field. Note: Results 
     * from this method are cached!
     *
     * @model   static
     * @access  public
     * @param   string class unqualified class name
     * @param   string method
     * @return  array
     */
    function detailsForField($class, $field) {
      $field= strtolower($field);
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[0][$field])) return $details[0][$field];
        $class= get_parent_class($class);
      }
      return NULL;
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
        $fname= strtr('.', '/', $name).'.class.php';
        $classPaths= &xp::registry('include_path');
        foreach ($classPaths as $path => $loader) {
          if (is_dir($path) && file_exists($path.DIRECTORY_SEPARATOR.$fname)) {
            $classloader= &$classPaths[$path];
            break;
          }
          
          if (is_file($path)) {
            $cl= &ArchiveClassLoader::instanceFor($path);
            if ($cl->providesClass($name)) {
              $classloader= &$cl;
              break;
            }
          }
        }
      }
      
      // Last-chance fallback
      if (NULL === $classloader) $classloader= &ClassLoader::getDefault();

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
    function &getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        if (xp::registry('class.'.$name)) $ret[]= &new XPClass($name);
      }
      return $ret;
    }
  }
?>
