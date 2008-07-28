<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.Type',
    'lang.reflect.Method',
    'lang.reflect.Field',
    'lang.reflect.Constructor',
    'lang.reflect.Modifiers',
    'lang.reflect.Package'
  );

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
   * Warning
   * =======
   * Do not construct this class publicly, instead use either the
   * $o->getClass() syntax or the static method 
   * $class= XPClass::forName('fully.qualified.Name')
   *
   * Examples
   * ========
   * To retrieve the fully qualified name of a class, use this:
   * <code>
   *   $o= new File('...');
   *   echo 'The class name for $o is '.$o->getClass()->getName();
   * </code>
   *
   * Create an instance of a class:
   * <code>
   *   $instance= XPClass::forName('util.Binford')->newInstance();
   * </code>
   *
   * Invoke a method by its name:
   * <code>
   *   try {
   *     $instance->getClass()->getMethod('connect')->invoke($instance);
   *   } catch (TargetInvocationException $e) {
   *     $e->getCause()->printStackTrace();
   *   }
   * </code> 
   *
   * @see      xp://lang.Object#getClass
   * @see      xp://lang.XPClass#forName
   * @test     xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @test     xp://net.xp_framework.unittest.reflection.ClassDetailsTest
   * @purpose  Reflection
   */
  class XPClass extends Type {
    protected
      $_class   = NULL;

    public 
      $_reflect = NULL;
      
    /**
     * Constructor
     *
     * @param   mixed ref either a class name, a ReflectionClass instance or an object
     */
    public function __construct($ref) {
      if ($ref instanceof ReflectionClass) {
        $this->_class= $ref->getName();
        $this->_reflect= $ref;
      } else if (is_object($ref)) {
        $this->_class= get_class($ref);
        $this->_reflect= new ReflectionClass($ref);
      } else {
        $this->_class= $ref;
        $this->_reflect= new ReflectionClass($ref);
      }
      parent::__construct(xp::nameOf($this->_class));
    }
    
    /**
     * Retrieves the package associated with this class
     * 
     * @return  lang.reflect.Package
     */
    public function getPackage() {
      return Package::forName(substr($this->name, 0, strrpos($this->name, '.')));
    }
    
    /**
     * Creates a new instance of the class represented by this Class object.
     * The class is instantiated as if by a new expression with an empty argument list.
     *
     * Example
     * =======
     * <code>
     *   try {
     *     $o= XPClass::forName($name)->newInstance();
     *   } catch (ClassNotFoundException $e) {
     *     // handle it!
     *   }
     * </code>
     *
     * Example (passing arguments)
     * ===========================
     * <code>
     *   try {
     *     $o= XPClass::forName('peer.Socket')->newInstance('localhost', 6100);
     *   } catch (ClassNotFoundException $e) {
     *     // handle it!
     *   }
     * </code>
     *
     * @param   mixed* args
     * @return  lang.Object 
     * @throws  lang.IllegalAccessException in case this class cannot be instantiated
     */
    public function newInstance() {
      if ($this->_reflect->isInterface()) {
        throw new IllegalAccessException('Cannot instantiate interfaces');
      } else if ($this->_reflect->isAbstract()) {
        throw new IllegalAccessException('Cannot instantiate abstract classes');
      }
      
      try {
        if (!$this->hasConstructor()) return $this->_reflect->newInstance();
        $args= func_get_args();
        return $this->_reflect->newInstanceArgs($args);
      } catch (ReflectionException $e) {
        throw new IllegalAccessException($e->getMessage());
      }
    }
    
    /**
     * Gets class methods for this class
     *
     * @return  lang.reflect.Method[]
     */
    public function getMethods() {
      $list= array();
      foreach ($this->_reflect->getMethods() as $m) {
        if (0 == strncmp('__', $m->getName(), 2)) continue;
        $list[]= new Method($this->_class, $m);
      }
      return $list;
    }

    /**
     * Gets a method by a specified name. Returns NULL if the specified 
     * method does not exist.
     *
     * @param   string name
     * @return  lang.reflect.Method
     * @see     xp://lang.reflect.Method
     */
    public function getMethod($name) {
      if ($this->hasMethod($name)) {
        return new Method($this->_class, $this->_reflect->getMethod($name));
      }
      return NULL;
    }
    
    /**
     * Checks whether this class has a method named "$method" or not.
     *
     * Note
     * ====
     * Since in PHP, methods are case-insensitive, calling hasMethod('toString') 
     * will provide the same result as hasMethod('tostring')
     *
     * @param   string method the method's name
     * @return  bool TRUE if method exists
     */
    public function hasMethod($method) {
      return ((0 === strncmp('__', $method, 2))
        ? FALSE
        : $this->_reflect->hasMethod($method)
      );
    }
    
    /**
     * Retrieve if a constructor exists
     *
     * @return  bool
     */
    public function hasConstructor() {
      return $this->_reflect->hasMethod('__construct');
    }
    
    /**
     * Retrieves this class' constructor. Returns NULL if no constructor
     * exists.
     *
     * @return  lang.reflect.Constructor
     * @see     xp://lang.reflect.Constructor
     */
    public function getConstructor() {
      if ($this->hasConstructor()) {
        return new Constructor($this->_class, $this->_reflect->getMethod('__construct')); 
      }
      return NULL;
    }
    
    /**
     * Retrieve a list of all member variables
     *
     * @return  lang.reflect.Field[] array of field objects
     */
    public function getFields() {
      $f= array();
      foreach ($this->_reflect->getProperties() as $p) {
        if ('__id' === $p->getName()) continue;
        $f[]= new Field($this->_class, $p);
      }
      return $f;
    }
    
    /**
     * Retrieve a field by a specified name. Returns NULL if the specified
     * field does not exist
     *
     * @param   string name
     * @return  lang.reflect.Field
     */
    public function getField($name) {
      if (!$this->hasField($name)) return NULL;

      return new Field($this->_class, $this->_reflect->getProperty($name));
    }
    
    /**
     * Checks whether this class has a field named "$field" or not.
     *
     * @param   string field the fields's name
     * @return  bool TRUE if field exists
     */
    public function hasField($field) {
      return '__id' == $field ? FALSE : $this->_reflect->hasProperty($field);
    }

    /**
     * Retrieve the parent class's class object. Returns NULL if there
     * is no parent class.
     *
     * @return  lang.XPClass class object
     */
    public function getParentclass() {
      return ($parent= $this->_reflect->getParentClass()) ? new self($parent) : NULL;
    }

    /**
     * Cast a given object to the class represented by this object
     *
     * @param   lang.Generic expression
     * @return  lang.Generic the given expression
     * @throws  lang.ClassCastException
     */
    public function cast(Generic $expression= NULL) {
      if (NULL === $expression) {
        return xp::null();
      } else if (is($this->name, $expression)) {
        return $expression;
      }
      raise('lang.ClassCastException', 'Cannot cast '.xp::typeOf($expression).' to '.$this->name);
    }
    
    /**
     * Tests whether this class is a subclass of a specified class.
     *
     * @param   string name class name
     * @return  bool
     */
    public function isSubclassOf($name) {
      if ($name == $this->name) return FALSE;   // Catch bordercase (ZE bug?)
      return $this->_reflect->isSubclassOf(XPClass::forName($name)->_reflect);
    }

    /**
     * Determines whether the specified object is an instance of this
     * class. This is the equivalent of the is() core functionality.
     *
     * Examples
     * ========
     * <code>
     *   uses('io.File', 'io.TempFile');
     *   $class= XPClass::forName('io.File');
     * 
     *   var_dump($class->isInstance(new TempFile()));  // TRUE
     *   var_dump($class->isInstance(new File()));      // TRUE
     *   var_dump($class->isInstance(new Object()));    // FALSE
     * </code>
     *
     * @param   lang.Object obj
     * @return  bool
     */
    public function isInstance($obj) {
      return is($this->name, $obj);
    }
    
    /**
     * Determines if this XPClass object represents an interface type.
     *
     * @return  bool
     */
    public function isInterface() {
      return $this->_reflect->isInterface();
    }

    /**
     * Determines if this XPClass object represents an interface type.
     *
     * @return  bool
     */
    public function isEnum() {
      $e= xp::reflect('lang.Enum');
      return class_exists($e) && $this->_reflect->isSubclassOf($e);
    }
    
    /**
     * Retrieve interfaces this class implements
     *
     * @return  lang.XPClass[]
     */
    public function getInterfaces() {
      $r= array();
      foreach ($this->_reflect->getInterfaces() as $iface) {
        $r[]= new self($iface->getName());
      }
      return $r;
    }

    /**
     * Retrieves the api doc comment for this class. Returns NULL if
     * no documentation is present.
     *
     * @return  string
     */
    public function getComment() {
      if (!($details= self::detailsForClass($this->name))) return NULL;
      return $details['class'][DETAIL_COMMENT];
    }

    /**
     * Retrieves this class' modifiers
     *
     * @see     xp://lang.reflect.Modifiers
     * @return  int
     */
    public function getModifiers() {
      $r= MODIFIER_PUBLIC;

      // Map PHP reflection modifiers to generic form
      $m= $this->_reflect->getModifiers();
      $m & ReflectionClass::IS_EXPLICIT_ABSTRACT && $r |= MODIFIER_ABSTRACT;
      $m & ReflectionClass::IS_IMPLICIT_ABSTRACT && $r |= MODIFIER_ABSTRACT;
      $m & ReflectionClass::IS_FINAL && $r |= MODIFIER_FINAL;
      
      return $r;
    }

    /**
     * Check whether an annotation exists
     *
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      $details= self::detailsForClass($this->name);

      return $details && ($key 
        ? array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      );
    }

    /**
     * Retrieve annotation by name
     *
     * @param   string name
     * @param   string key default NULL
     * @return  mixed
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      $details= self::detailsForClass($this->name);

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
     * @return  bool
     */
    public function hasAnnotations() {
      $details= self::detailsForClass($this->name);
      return $details ? !empty($details['class'][DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of a method's annotations
     *
     * @return  array annotations
     */
    public function getAnnotations() {
      $details= self::detailsForClass($this->name);
      return $details ? $details['class'][DETAIL_ANNOTATIONS] : array();
    }
    
    /**
     * Retrieve the class loader a class was loaded with
     *
     * @return  lang.IClassLoader
     */
    public function getClassLoader() {
      return self::_classLoaderFor($this->name);
    }
    
    /**
     * Fetch a class' classloader by its name
     *
     * @param   string name fqcn of class
     * @return  lang.IClassLoader
     */
    protected static function _classLoaderFor($name) {
      sscanf(xp::$registry['classloader.'.$name], '%[^:]://%[^$]', $cl, $argument);
      return call_user_func(array(xp::reflect($cl), 'instanceFor'), $argument);
    }

    /**
     * Retrieve details for a specified class. Note: Results from this 
     * method are cached!
     *
     * @param   string class fully qualified class name
     * @return  array or NULL to indicate no details are available
     */
    public static function detailsForClass($class) {
      static $details= array();

      if (!$class) return NULL;        // Border case
      if (isset($details[$class])) return $details[$class];

      // Retrieve class' sourcecode
      if (!($bytes= self::_classLoaderFor($class)->loadClassBytes($class))) return NULL;

      $details[$class]= array(array(), array());
      $annotations= array();
      $comment= NULL;
      $members= TRUE;
      $tokens= token_get_all($bytes);
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case T_DOC_COMMENT:
            $comment= $tokens[$i][1];
            break;

          case T_COMMENT:

            // Annotations
            if (strncmp('#[@', $tokens[$i][1], 3) == 0) {
              $annotations[0]= substr($tokens[$i][1], 2);
            } else if (strncmp('#', $tokens[$i][1], 1) == 0) {
              $annotations[0].= substr($tokens[$i][1], 1);
            }

            // End of annotations
            if (']' == substr(rtrim($tokens[$i][1]), -1)) {
              $annotations= eval('return array('.preg_replace(
                array('/@([a-z_]+),/i', '/@([a-z_]+)\(\'([^\']+)\'\)/i', '/@([a-z_]+)\(/i', '/([^a-z_@])([a-z_]+) *= */i'),
                array('\'$1\' => NULL,', '\'$1\' => \'$2\'', '\'$1\' => array(', '$1\'$2\' => '),
                trim($annotations[0], "[]# \t\n\r").','
              ).');');
            }
            break;

          case T_CLASS:
          case T_INTERFACE:
            $details[$class]['class']= array(
              DETAIL_COMMENT      => trim(preg_replace('/\n   \* ?/', "\n", "\n".substr(
                $comment, 
                4,                              // "/**\n"
                strpos($comment, '* @')- 2      // position of first details token
              ))),
              DETAIL_ANNOTATIONS  => $annotations
            );
            $annotations= array();
            $comment= NULL;
            break;

          case T_VARIABLE:
            if (!$members) break;

            // Have a member variable
            $name= substr($tokens[$i][1], 1);
            $details[$class][0][$name]= array(
              DETAIL_ANNOTATIONS => $annotations
            );
            $annotations= array();
            break;

          case T_FUNCTION:
            $members= FALSE;
            while (T_STRING !== $tokens[$i][0]) $i++;
            $m= $tokens[$i][1];
            $details[$class][1][$m]= array(
              DETAIL_ARGUMENTS    => array(),
              DETAIL_RETURNS      => 'void',
              DETAIL_THROWS       => array(),
              DETAIL_COMMENT      => trim(preg_replace('/\n     \* ?/', "\n", "\n".substr(
                $comment, 
                4,                              // "/**\n"
                strpos($comment, '* @')- 2      // position of first details token
              ))),
              DETAIL_ANNOTATIONS  => $annotations,
              DETAIL_NAME         => $tokens[$i][1]
            );
            $matches= NULL;
            preg_match_all(
              '/@([a-z]+)\s*([^<\r\n]+<[^>]+>|[^\r\n ]+) ?([^\r\n ]+)?/',
              $comment, 
              $matches, 
              PREG_SET_ORDER
            );
            $annotations= array();
            $comment= NULL;
            foreach ($matches as $match) {
              switch ($match[1]) {
                case 'param':
                  $details[$class][1][$m][DETAIL_ARGUMENTS][]= $match[2];
                  break;

                case 'return':
                  $details[$class][1][$m][DETAIL_RETURNS]= $match[2];
                  break;

                case 'throws': 
                  $details[$class][1][$m][DETAIL_THROWS][]= $match[2];
                  break;
              }
            }
            break;

          default:
            // Empty
        }
      }
      
      // Return details for specified class
      return $details[$class]; 
    }

    /**
     * Retrieve details for a specified class and method. Note: Results 
     * from this method are cached!
     *
     * @param   string class unqualified class name
     * @param   string method
     * @return  array
     */
    public static function detailsForMethod($class, $method) {
      while ($details= self::detailsForClass(xp::nameOf($class))) {
        if (isset($details[1][$method])) return $details[1][$method];
        $class= get_parent_class($class);
      }
      return NULL;
    }

    /**
     * Retrieve details for a specified class and field. Note: Results 
     * from this method are cached!
     *
     * @param   string class unqualified class name
     * @param   string method
     * @return  array
     */
    public static function detailsForField($class, $field) {
      while ($details= self::detailsForClass(xp::nameOf($class))) {
        if (isset($details[0][$field])) return $details[0][$field];
        $class= get_parent_class($class);
      }
      return NULL;
    }
    
    /**
     * Returns the XPClass object associated with the class with the given 
     * string name. Uses the default classloader if none is specified.
     *
     * @param   string name - e.g. "io.File", "rdbms.mysql.MySQL"
     * @param   lang.IClassLoader classloader default NULL
     * @return  lang.XPClass class object
     * @throws  lang.ClassNotFoundException when there is no such class
     */
    public static function forName($name, IClassLoader $classloader= NULL) {
      if (NULL === $classloader) {
        $classloader= ClassLoader::getDefault();
      }

      return $classloader->loadClass($name);
    }
    
    /**
     * Returns an array containing class objects representing all the 
     * public classes
     *
     * @return  lang.XPClass[] class objects
     */
    public static function getClasses() {
      $ret= array();
      foreach (get_declared_classes() as $name) {
        if (xp::registry('class.'.$name)) $ret[]= new self($name);
      }
      return $ret;
    }
  }
?>
