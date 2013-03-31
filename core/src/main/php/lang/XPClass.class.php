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
  define('DETAIL_TARGET_ANNO',    6);
  define('DETAIL_GENERIC',        7);
 
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
   * @test     xp://net.xp_framework.unittest.reflection.IsInstanceTest
   * @test     xp://net.xp_framework.unittest.reflection.ClassCastingTest
   * @purpose  Reflection
   */
  class XPClass extends Type {
    protected $_class= NULL;
    public $_reflect= NULL;
    
    /**
     * Constructor
     *
     * @param   var ref either a class name, a ReflectionClass instance or an object
     * @throws  lang.IllegalStateException
     */
    public function __construct($ref) {
      if ($ref instanceof ReflectionClass) {
        $this->_reflect= $ref;
        $this->_class= $ref->getName();
      } else if (is_object($ref)) {
        $this->_reflect= new ReflectionClass($ref);
        $this->_class= get_class($ref);
      } else {
        try {
          $this->_reflect= new ReflectionClass((string)$ref);
        } catch (ReflectionException $e) {
          throw new IllegalStateException($e->getMessage());
        }
        $this->_class= $ref;
      }
      parent::__construct(xp::nameOf($this->_class));
    }
    
    /**
     * Returns simple name
     *
     * @return  string
     */
    public function getSimpleName() {
      return FALSE === ($p= strrpos(substr($this->name, 0, strcspn($this->name, '<')), '.')) 
        ? $this->name                   // Already unqualified
        : substr($this->name, $p+ 1)    // Full name
      ;
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
     * @param   var* args
     * @return  lang.Object 
     * @throws  lang.IllegalAccessException in case this class cannot be instantiated
     */
    public function newInstance() {
      if ($this->_reflect->isInterface()) {
        throw new IllegalAccessException('Cannot instantiate interfaces ('.$this->name.')');
      } else if ($this->_reflect->isAbstract()) {
        throw new IllegalAccessException('Cannot instantiate abstract classes ('.$this->name.')');
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
     * Gets class methods declared by this class
     *
     * @return  lang.reflect.Method[]
     */
    public function getDeclaredMethods() {
      $list= array();
      foreach ($this->_reflect->getMethods() as $m) {
        if (0 == strncmp('__', $m->getName(), 2) || $m->class !== $this->_reflect->name) continue;
        $list[]= new Method($this->_class, $m);
      }
      return $list;
    }

    /**
     * Gets a method by a specified name.
     *
     * @param   string name
     * @return  lang.reflect.Method
     * @see     xp://lang.reflect.Method
     * @throws  lang.ElementNotFoundException
     */
    public function getMethod($name) {
      if ($this->hasMethod($name)) {
        return new Method($this->_class, $this->_reflect->getMethod($name));
      }
      raise('lang.ElementNotFoundException', 'No such method "'.$name.'" in class '.$this->name);
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
     * Retrieves this class' constructor.
     *
     * @return  lang.reflect.Constructor
     * @see     xp://lang.reflect.Constructor
     * @throws  lang.ElementNotFoundException
     */
    public function getConstructor() {
      if ($this->hasConstructor()) {
        return new Constructor($this->_class, $this->_reflect->getMethod('__construct')); 
      }
      raise('lang.ElementNotFoundException', 'No constructor in class '.$this->name);
    }
    
    /**
     * Retrieve a list of all member variables
     *
     * @return  lang.reflect.Field[] array of field objects
     */
    public function getFields() {
      $f= array();
      foreach ($this->_reflect->getProperties() as $p) {
        if ('__id' === $p->name) continue;
        $f[]= new Field($this->_class, $p);
      }
      return $f;
    }

    /**
     * Retrieve a list of member variables declared in this class
     *
     * @return  lang.reflect.Field[] array of field objects
     */
    public function getDeclaredFields() {
      $list= array();
      foreach ($this->_reflect->getProperties() as $p) {
        if ('__id' === $p->name || $p->class !== $this->_reflect->name) continue;
        $list[]= new Field($this->_class, $p);
      }
      return $list;
    }

    /**
     * Retrieve a field by a specified name.
     *
     * @param   string name
     * @return  lang.reflect.Field
     * @throws  lang.ElementNotFoundException
     */
    public function getField($name) {
      if ($this->hasField($name)) {
        return new Field($this->_class, $this->_reflect->getProperty($name));
      }
      raise('lang.ElementNotFoundException', 'No such field "'.$name.'" in class '.$this->name);
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
     * Checks whether this class has a constant named "$constant" or not
     *
     * @param   string constant
     * @return  bool
     */
    public function hasConstant($constant) {
      return $this->_reflect->hasConstant($constant);
    }
    
    /**
     * Retrieve a constant by a specified name.
     *
     * @param   string constant
     * @return  var
     * @throws  lang.ElementNotFoundException in case constant does not exist
     */
    public function getConstant($constant) {
      if ($this->hasConstant($constant)) {
        return $this->_reflect->getConstant($constant);
      }
      
      raise('lang.ElementNotFoundException', 'No such constants "'.$constant.'" in class '.$this->name);
    }

    /**
     * Retrieve class constants
     *
     * @return  [:var]
     */
    public function getConstants() {
      return $this->_reflect->getConstants();
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
     * @param   var class either a string or an XPClass object
     * @return  bool
     */
    public function isSubclassOf($class) {
      if (!($class instanceof self)) $class= XPClass::forName($class);
      if ($class->name == $this->name) return FALSE;   // Catch bordercase (ZE bug?)
      return $this->_reflect->isSubclassOf($class->_reflect);
    }

    /**
     * Tests whether this class is assignable from a given type
     *
     * <code>
     *   // util.Date instanceof lang.Object
     *   XPClass::forName('lang.Object')->isAssignableFrom('util.Date');   // TRUE
     * </code>
     *
     * @param   var type
     * @return  bool
     */
    public function isAssignableFrom($type) {
      $t= $type instanceof Type ? $type : Type::forName($type);
      return $t instanceof self
        ? $t->name === $this->name || $t->_reflect->isSubclassOf($this->_reflect)
        : FALSE
      ;
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
     * @param   var obj
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
      return class_exists($e, FALSE) && $this->_reflect->isSubclassOf($e);
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
     * Retrieve interfaces this class implements in its declaration
     *
     * @return  lang.XPClass[]
     */
    public function getDeclaredInterfaces() {
      $is= $this->_reflect->getInterfaces();
      if ($parent= $this->_reflect->getParentclass()) {
        $ip= $parent->getInterfaces();
      } else {
        $ip= array();
      }
      $filter= array();
      foreach ($is as $iname => $i) {

        // Parent class implements this interface
        if (isset($ip[$iname])) {
          $filter[$iname]= TRUE;
          continue;
        }

        // Interface is implemented because it's the parent of another interface
        foreach ($i->getInterfaces() as $pname => $p) {
          if (isset($is[$pname])) $filter[$pname]= TRUE;
        }
      }
      
      $r= array();
      foreach ($is as $iname => $i) {
        if (!isset($filter[$iname])) $r[]= new self($i);
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
        ? @array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : @array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
      );
    }

    /**
     * Retrieve annotation by name
     *
     * @param   string name
     * @param   string key default NULL
     * @return  var
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      $details= self::detailsForClass($this->name);

      if (!$details || !($key 
        ? @array_key_exists($key, @$details['class'][DETAIL_ANNOTATIONS][$name]) 
        : @array_key_exists($name, @$details['class'][DETAIL_ANNOTATIONS])
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
     * Retrieve the class loader a class was loaded with.
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
      if (isset(xp::$cl[$name])) {
        sscanf(xp::$cl[$name], '%[^:]://%[^$]', $cl, $argument);
        return call_user_func(array(xp::reflect($cl), 'instanceFor'), $argument);
      }
      return NULL;    // Internal class, e.g.
    }
    
    /**
     * Parses annotation string
     *
     * @param   string input
     * @param   string context the class name, and optionally the line number.
     * @return  [:var]
     * @throws  lang.ClassFormatException
     */
    public static function parseAnnotations($input, $context) {
      $input= trim($input, "[]# \t\n\r").']';
      $offset= 0;
      $annotations= array(0 => array(), 1 => array());
      $annotation= $value= NULL;
      $length= strlen($input);
      ob_start();
      while ($offset < $length) {
        $state= $input{$offset};
        if ('@' === $state) {
          $s= strcspn($input, ',(]', $offset);
          $annotation= substr($input, $offset+ 1, $s- 1);
          $offset+= $s;
        } else if (']' === $state) {
          if (NULL === $annotation) {
            // Nothing
          } else if (FALSE === ($p= strpos($annotation, ':'))) {
            $annotations[0][$annotation]= $value;
          } else {
            $target= rtrim(substr($annotation, 0, $p), ' ');
            $annotation= ltrim(substr($annotation, $p+ 1), ' ');
            isset($annotations[1][$target]) || $annotations[1][$target]= array();
            $annotations[1][$target][$annotation]= $value;
          }
          break;
        } else if ('(' === $state) {
          $peek= substr($input, $offset+ 1, strcspn($input, '="\')', $offset));
          if ('\'' === $peek{0} || '"' === $peek{0}) {
            $p= $offset+ 2;
            $q= $peek{0};
            while (($s= strcspn($input, $q, $p)) !== 0) {
              $p+= $s;
              if ('\\' !== $input{$p- 1}) break;
              $p++;   
            }
            if (!is_string($value= @eval('return '.substr($input, $offset+ 1, $p - $offset).';'))) {
              raise('lang.ClassFormatException', 'Parse error: Unterminated or malformed string in '.$context);
            }
            $offset= $p+ 1;
          } else if ('[' === ($a= $peek{0}) || 'array(' === ($a= substr($peek, 0, 6))) {
            $la= strlen($a);
            $ba= '[' === $a ? '[]' : '()';
            $b= 1;
            $p= $offset+ $la+ 1;
            while ($b > 0) {
              $p+= strcspn($input, $ba.'"\'', $p);
              if ($p >= $length) {
                raise('lang.ClassFormatException', 'Parse error: Unterminated array in '.$context);
              }
              if ($ba{0} === $input{$p}) $b++; else if ($ba{1} === $input{$p}) $b--;else if ('\'' === $input{$p} || '"' === $input{$p}) {
                $q= $input{$p};
                $p++;
                while (($s= strcspn($input, $q, $p)) !== 0) {
                  $p+= $s;
                  if ('\\' !== $input{$p- 1}) break;
                  $p++;
                }
              }
              $p++;
            }
            if ($p >= $length || !is_array($value= @eval('return array('.substr($input, $offset+ $la+ 1, $p- $offset- $la- 1- 1).');'))) {
              raise('lang.ClassFormatException', 'Parse error: Malformed array in '.$context);
            }
            $offset= $p;
          } else if ('=' !== $peek{strlen($peek)- 1}) {
            $value= eval('return '.substr($peek, 0, -1).';');
            $offset+= strlen($peek);
          } else {
            $value= array();
            do {
              $key= trim($peek, '= ');
              $offset+= strlen($peek)+ 1;
              $offset+= strspn($input, ' ', $offset);
              if ($offset >= $length) {
                break;
              } else if ('[' === ($a= $input{$offset}) || 'array(' === ($a= substr($input, $offset, 6))) {
                $la= strlen($a);
                $ba= '[' === $a ? '[]' : '()';
                $b= 1;
                $p= $offset+ $la;
                while ($b > 0) {
                  $p+= strcspn($input, $ba.'"\'', $p);
                  if ($p >= $length) {
                    raise('lang.ClassFormatException', 'Parse error: Unterminated array in '.$context);
                  }
                  if ($ba{0} === $input{$p}) $b++; else if ($ba{1} === $input{$p}) $b--; else if ('\'' === $input{$p} || '"' === $input{$p}) {
                    $q= $input{$p};
                    $p++;
                    while (($s= strcspn($input, $q, $p)) !== 0) {
                      $p+= $s;
                      if ('\\' !== $input{$p- 1}) break;
                      $p++;
                    }
                  }
                  $p++;
                }
                if (!is_array($value[$key]= @eval('return array('.substr($input, $offset+ $la, $p- $offset- $la- 1).');'))) {
                  raise('lang.ClassFormatException', 'Parse error: Malformed array in '.$context);
                }
                $offset= $p;
              } else if ('\'' === $input{$offset} || '"' === $input{$offset}) {
                $p= $offset+ 1;
                $q= $input{$offset};
                while (($s= strcspn($input, $q, $p)) !== 0) {
                  $p+= $s;
                  if ('\\' !== $input{$p- 1}) break;
                  $p++;   
                }
                if (!is_string($value[$key]= @eval('return '.substr($input, $offset, $p - $offset + 1).';'))) {
                  raise('lang.ClassFormatException', 'Parse error: Unterminated or malformed string in '.$context);
                }
                $offset= $p+ 1;
              } else {
                $s= strcspn($input, ',)', $offset);
                $value[$key]= eval('return '.substr($input, $offset, $s).';');
                $offset+= $s;
              }
              
              // Find next key
              $s= strcspn($input, '="\')', $offset);
              $peek= substr($input, $offset+ 1, $s);
            } while ($s);
          }
          if ($offset >= $length) {
            raise('lang.ClassFormatException', 'Parse error: Expecting ] in '.$context);
          }
        } else if (')' === $state) {
          if (FALSE === ($p= strpos($annotation, ':'))) {
            $annotations[0][$annotation]= $value;
          } else {
            $target= rtrim(substr($annotation, 0, $p), ' ');
            $annotation= ltrim(substr($annotation, $p+ 1), ' ');
            isset($annotations[1][$target]) || $annotations[1][$target]= array();
            $annotations[1][$target][$annotation]= $value;
          }
          $annotation= $value= NULL;
          $s= strspn($input, ',]', $offset);
          $offset+= $s + 1;
        } else if (',' === $state) {
          if (NULL !== $annotation && NULL !== $value) {    // BC
            trigger_error('Deprecated usage of multi-value annotations in '.$context, E_USER_DEPRECATED);
            $annotations[0][$annotation]= array($value);
            do {
              $s= strspn($input, ' "\')', $offset+ 1);
              $offset+= $s;
              if ('\'' === $input{$offset} || '"' === $input{$offset}) {
                $p= $offset+ 1;
                $q= $input{$offset};
                while (($s= strcspn($input, $q, $p)) !== 0) {
                  $p+= $s;
                  if ('\\' !== $input{$p- 1}) break;
                  $p++;
                }
                if (!is_string($value= @eval('return '.substr($input, $offset, $p - $offset+ 1).';'))) {
                  raise('lang.ClassFormatException', 'Parse error: Unterminated or malformed string in '.$context);
                }
                $offset= $p+ 1;
                $annotations[0][$annotation][]= $value;
              } else if (')' === $input{$offset}) {
                break;
              } else {
                $s= strcspn($input, ',)', $offset);
                $annotations[0][$annotation][]= eval('return '.substr($input, $offset, $s).';');
                $offset+= $s;
              }
            } while ($offset <= $length);
            break;
          }
          if (NULL === $annotation) {
            // Nothing
          } else if (FALSE === ($p= strpos($annotation, ':'))) {
            $annotations[0][$annotation]= $value;
          } else {
            $target= rtrim(substr($annotation, 0, $p), ' ');
            $annotation= ltrim(substr($annotation, $p+ 1), ' ');
            isset($annotations[1][$target]) || $annotations[1][$target]= array();
            $annotations[1][$target][$annotation]= $value;
          }
          $annotation= $value= NULL;
          if (FALSE === ($offset= strpos($input, '@', $offset))) {
            raise('lang.ClassFormatException', 'Parse error: Expecting @ in '.$context);
          }
        } else {
          raise('lang.ClassFormatException', 'Parse error: Unknown state '.$state.' at position '.$offset.' in '.$context);
        }
      }
      $error= ob_get_contents();
      ob_end_clean();
      if ($error) {
        raise('lang.ClassFormatException', trim($error).' in '.$context);
      }
      
      return $annotations;
    }

    /**
     * Parse details from a given input string
     *
     * @param   string bytes
     * @param   string context default ''
     * @return  [:var] details
     */
    public static function parseDetails($bytes, $context= '') {
      $details= array(array(), array());
      $annotations= array(0 => array(), 1 => array());
      $comment= NULL;
      $members= TRUE;
      $parsed= '';
      $tokens= token_get_all($bytes);
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case T_DOC_COMMENT:
            $comment= $tokens[$i][1];
            break;

          case T_COMMENT:
            if ('#' === $tokens[$i][1]{0}) {      // Annotations
              if ('[' === $tokens[$i][1]{1}) {
                $parsed= substr($tokens[$i][1], 2);
              } else {
                $parsed.= substr($tokens[$i][1], 1);
              }
              if (']' == substr(rtrim($tokens[$i][1]), -1)) {
                $annotations= self::parseAnnotations(
                  trim($parsed, " \t\n\r"), 
                  $context.(isset($tokens[$i][2]) ? ', line '.$tokens[$i][2] : '')
                );
                $parsed= '';
              }
            }
            break;

          case T_CLASS:
          case T_INTERFACE:
            if ('' !== $parsed) raise(
              'lang.ClassFormatException', 
              'Unterminated annotation "'.addcslashes($parsed, "\0..\17").'" in '.$context.(isset($tokens[$i][2]) ? ', line '.$tokens[$i][2] : '')
            );
            $details['class']= array(
              DETAIL_COMMENT      => trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
                $comment, 
                4,                              // "/**\n"
                strpos($comment, '* @')- 2      // position of first details token
              ))),
              DETAIL_ANNOTATIONS  => $annotations[0]
            );
            $annotations= array(0 => array(), 1 => array());
            $comment= NULL;
            break;

          case T_VARIABLE:
            if (!$members) break;

            // Have a member variable
            '' === $parsed || raise('lang.ClassFormatException', 'Unterminated annotation "'.addcslashes($parsed, "\0..\17").'" in '.$context.', line '.(isset($tokens[$i][2]) ? ', line '.$tokens[$i][2] : ''));
            $name= substr($tokens[$i][1], 1);
            $details[0][$name]= array(
              DETAIL_ANNOTATIONS => $annotations[0]
            );
            $annotations= array(0 => array(), 1 => array());
            break;

          case T_FUNCTION:
            if (T_STRING !== $tokens[$i+ 2][0]) break;    // A closure, `function($params) { return TRUE; }`
            '' === $parsed || raise('lang.ClassFormatException', 'Unterminated annotation "'.addcslashes($parsed, "\0..\17").'" in '.$context.', line '.(isset($tokens[$i][2]) ? ', line '.$tokens[$i][2] : ''));
            $members= FALSE;
            $i+= 2;
            $m= $tokens[$i][1];
            $details[1][$m]= array(
              DETAIL_ARGUMENTS    => array(),
              DETAIL_RETURNS      => NULL === $comment ? NULL : 'void',
              DETAIL_THROWS       => array(),
              DETAIL_COMMENT      => trim(preg_replace('/\n\s+\* ?/', "\n", "\n".substr(
                $comment, 
                4,                              // "/**\n"
                strpos($comment, '* @')- 2      // position of first details token
              ))),
              DETAIL_ANNOTATIONS  => $annotations[0],
              DETAIL_TARGET_ANNO  => $annotations[1]
            );
            $annotations= array(0 => array(), 1 => array());
            $matches= NULL;
            preg_match_all(
              '/@([a-z]+)\s*([^<\r\n]+<[^>]+>|[^\r\n ]+) ?([^\r\n ]+)?/',
              $comment, 
              $matches, 
              PREG_SET_ORDER
            );
            $comment= NULL;
            $arg= 0;
            foreach ($matches as $match) {
              switch ($match[1]) {
                case 'param':
                  $details[1][$m][DETAIL_ARGUMENTS][$arg++]= $match[2];
                  break;

                case 'return':
                  $details[1][$m][DETAIL_RETURNS]= $match[2];
                  break;

                case 'throws': 
                  $details[1][$m][DETAIL_THROWS][]= $match[2];
                  break;
              }
            }
            break;

          default:
            // Empty
        }
      }
      return $details;
    }

    /**
     * Retrieve details for a specified class. Note: Results from this 
     * method are cached!
     *
     * @param   string class fully qualified class name
     * @return  array or NULL to indicate no details are available
     */
    public static function detailsForClass($class) {
      if (!$class) {                                             // Border case
        return NULL;
      } else if (isset(xp::$meta[$class])) {                     // Cached
        return xp::$meta[$class];
      } else if (isset(xp::$registry[$l= 'details.'.$class])) {  // BC: Cached in registry
        return xp::$registry[$l];
      }

      // Retrieve class' sourcecode
      $cl= self::_classLoaderFor($class);
      if (!$cl || !($bytes= $cl->loadClassBytes($class))) return NULL;

      // Return details for specified class
      return xp::$meta[$class]= self::parseDetails($bytes, $class);
    }

    /**
     * Retrieve details for a specified class and method. Note: Results 
     * from this method are cached!
     *
     * @param   string class unqualified class name
     * @param   string method
     * @return  array or NULL if not available
     */
    public static function detailsForMethod($class, $method) {
      $details= self::detailsForClass(xp::nameOf($class));
      return $details ? (isset($details[1][$method]) ? $details[1][$method] : NULL) : NULL;
    }

    /**
     * Retrieve details for a specified class and field. Note: Results 
     * from this method are cached!
     *
     * @param   string class unqualified class name
     * @param   string method
     * @return  array or NULL if not available
     */
    public static function detailsForField($class, $field) {
      $details= self::detailsForClass(xp::nameOf($class));
      return $details ? (isset($details[0][$field]) ? $details[0][$field] : NULL) : NULL;
    }
    
    /**
     * Creates a generic type
     *
     * @param   lang.XPClass self
     * @param   lang.Type[] arguments
     * @return  string created type's literal name
     */
    public static function createGenericType(XPClass $self, array $arguments) {

      // Verify
      $annotations= $self->getAnnotations();
      if (!isset($annotations['generic']['self'])) {
        throw new IllegalStateException('Class '.$self->name.' is not a generic definition');
      }
      $components= array();
      foreach (explode(',', $annotations['generic']['self']) as $cs => $name) {
        $components[]= ltrim($name);
      }
      $cs++;
      if ($cs !== sizeof($arguments)) {
        throw new IllegalArgumentException(sprintf(
          'Class %s expects %d component(s) <%s>, %d argument(s) given',
          $self->name,
          $cs,
          implode(', ', $components),
          sizeof($arguments)
        ));
      }
    
      // Compose names
      $cn= $qc= '';
      foreach ($arguments as $typearg) {
        $cn.= '¸'.strtr($typearg->literal(), '\\', '¦');
        $qc.= ','.$typearg->getName();
      }
      $name= $self->literal().'··'.substr($cn, 1);
      $qname= $self->name.'<'.substr($qc, 1).'>';

      // Create class if it doesn't exist yet
      if (!class_exists($name, FALSE) && !interface_exists($name, FALSE)) {
        $meta= xp::$meta[$self->name];

        // Parse placeholders into a lookup map
        $placeholders= array();
        foreach ($components as $i => $component) {
          $placeholders[$component]= $arguments[$i]->getName();
        }

        // Work on sourcecode
        $cl= self::_classLoaderFor($self->name);
        if (!$cl || !($bytes= $cl->loadClassBytes($self->name))) {
          throw new IllegalStateException($self->name);
        }

        // Replace source
        $src= '';
        $annotation= NULL;
        $matches= array();
        $state= array(0);
        $counter= 0;
        $tokens= token_get_all($bytes);
        for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
          if (T_COMMENT === $tokens[$i][0]) {
            continue;
          } else if (0 === $state[0]) {
            if (T_ABSTRACT === $tokens[$i][0] || T_FINAL === $tokens[$i][0]) {
              $src.= $tokens[$i][1].' ';
            } else if (T_CLASS === $tokens[$i][0] || T_INTERFACE === $tokens[$i][0]) {
              $meta['class'][DETAIL_GENERIC]= array($self->name, $arguments);
              $src.= $tokens[$i][1].' '.$name;
              array_unshift($state, $tokens[$i][0]);
            }
            continue;
          } else if (T_CLASS === $state[0]) {
            if (T_EXTENDS === $tokens[$i][0]) {
              if (isset($annotations['generic']['parent'])) {
                $xargs= array();
                foreach (explode(',', $annotations['generic']['parent']) as $j => $placeholder) {
                  $xargs[]= Type::forName(strtr(ltrim($placeholder), $placeholders));
                }
                $src.= ' extends '.strtr(self::createGenericType($self->getParentClass(), $xargs), '\\', '¦');
              } else {
                $src.= ' extends '.$tokens[$i+ 2][1];
              }
            } else if (T_IMPLEMENTS === $tokens[$i][0]) {
              $src.= ' implements';
              $counter= 0;
              $annotation= @$annotations['generic']['implements'];
              array_unshift($state, 5);
            } else if ('{' === $tokens[$i][0]) {
              array_shift($state);
              array_unshift($state, 1);
              $src.= ' {';
            }
            continue;
          } else if (T_INTERFACE === $state[0]) {
            if (T_EXTENDS === $tokens[$i][0]) {
              $src.= ' extends';
              $counter= 0;
              $annotation= @$annotations['generic']['extends'];
              array_unshift($state, 5);
            } else if ('{' === $tokens[$i][0]) {
              array_shift($state);
              array_unshift($state, 1);
              $src.= ' {';
            }
            continue;
          } else if (1 === $state[0]) {             // Class body
            if (T_FUNCTION === $tokens[$i][0]) {
              $braces= 0;
              $parameters= $default= array();
              array_unshift($state, 3);
              array_unshift($state, 2);
              $m= $tokens[$i+ 2][1];
              $p= 0;
              $annotations= array($meta[1][$m][DETAIL_ANNOTATIONS], $meta[1][$m][DETAIL_TARGET_ANNO]);
            } else if ('}' === $tokens[$i][0]) {
              $src.= '}';
              break;
            } else if (T_CLOSE_TAG === $tokens[$i][0]) {
              break;
            }
          } else if (2 === $state[0]) {             // Method declaration
            if ('(' === $tokens[$i][0]) {
              $braces++;
            } else if (')' === $tokens[$i][0]) {
              $braces--;
              if (0 === $braces) {
                array_shift($state);
                $src.= ')';
                continue;
              }
            }
            if (T_VARIABLE === $tokens[$i][0]) {
              $parameters[]= $tokens[$i][1];
            } else if ('=' === $tokens[$i][0]) {
              $p= sizeof($parameters)- 1;
              $default[$p]= '';
            } else if (T_WHITESPACE !== $tokens[$i][0] && isset($default[$p])) {
              $default[$p].= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
            }
          } else if (3 === $state[0]) {             // Method body
            if (';' === $tokens[$i][0]) {
              // Abstract method
              if (isset($annotations[0]['generic']['return'])) {
                $meta[1][$m][DETAIL_RETURNS]= strtr($annotations[0]['generic']['return'], $placeholders);
              }
              if (isset($annotations[0]['generic']['params'])) {
                foreach (explode(',', $annotations[0]['generic']['params']) as $j => $placeholder) {
                  if ('' !== ($replaced= strtr(ltrim($placeholder), $placeholders))) {
                    $meta[1][$m][DETAIL_ARGUMENTS][$j]= $replaced;
                  }
                }
              }
              $annotations= array();
              unset($meta[1][$m][DETAIL_ANNOTATIONS]['generic']);
              array_shift($state);
            } else if ('{' === $tokens[$i][0]) {
              $braces= 1;
              array_shift($state);
              array_unshift($state, 4);
              $src.= '{';
              
              if (isset($annotations[0]['generic']['return'])) {
                $meta[1][$m][DETAIL_RETURNS]= strtr($annotations[0]['generic']['return'], $placeholders);
              }
              if (isset($annotations[0]['generic']['params'])) {
                $generic= array();
                foreach (explode(',', $annotations[0]['generic']['params']) as $j => $placeholder) {
                  if ('' === ($replaced= strtr(ltrim($placeholder), $placeholders))) {
                    $generic[$j]= NULL;
                  } else {
                    $meta[1][$m][DETAIL_ARGUMENTS][$j]= $replaced;
                    $generic[$j]= $replaced;
                  }
                }
                foreach ($generic as $j => $type) {
                  if (NULL === $type) {
                    continue;
                  } else if ('...' === substr($type, -3)) {
                    $src.= $j ? '$·args= array_slice(func_get_args(), '.$j.');' : '$·args= func_get_args();';
                    $src.= (
                      ' if (!is(\''.substr($generic[$j], 0, -3).'[]\', $·args)) throw new IllegalArgumentException('.
                      '"Vararg '.($j + 1).' passed to ".__METHOD__."'.
                      ' must be of '.$type.', ".xp::stringOf($·args)." given"'.
                      ');'
                    );
                  } else {
                    $src.= (
                      ' if ('.(isset($default[$j]) ? '('.$default[$j].' !== '.$parameters[$j].') && ' : '').
                      '!is(\''.$generic[$j].'\', '.$parameters[$j].')) throw new IllegalArgumentException('.
                      '"Argument '.($j + 1).' passed to ".__METHOD__."'.
                      ' must be of '.$type.', ".xp::typeOf('.$parameters[$j].')." given"'.
                      ');'
                    );
                  }
                }
              }

              $annotations= array();
              unset($meta[1][$m][DETAIL_ANNOTATIONS]['generic']);
              continue;
            }
          } else if (4 === $state[0]) {             // Method body
            if ('{' === $tokens[$i][0]) {
              $braces++;
            } else if ('}' === $tokens[$i][0]) {
              $braces--;
              if (0 === $braces) array_shift($state);
            }
          } else if (5 === $state[0]) {             // Implements (class), Extends (interface)
            if (T_STRING === $tokens[$i][0]) {
              if (isset($annotation[$counter])) {
                $iargs= array();
                foreach (explode(',', $annotation[$counter]) as $j => $placeholder) {
                  $iargs[]= Type::forName(strtr(ltrim($placeholder), $placeholders));
                }
                $src.= strtr(self::createGenericType(new XPClass(new ReflectionClass($tokens[$i][1])), $iargs), '\\', '¦');
              } else {
                $src.= $tokens[$i][1];
              }
              $counter++;
              continue;
            } else if ('{' === $tokens[$i][0]) {
              array_shift($state);
              array_unshift($state, 1);
            }
          }
                    
          $src.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
        }

        // Create class
        // DEBUG fputs(STDERR, "@* ".substr($src, 0, strpos($src, '{'))." -> $qname\n");
        eval($src);
        method_exists($name, '__static') && call_user_func(array($name, '__static'));
        unset($meta['class'][DETAIL_ANNOTATIONS]['generic']);
        xp::$meta[$qname]= $meta;
        xp::$cn[$name]= $qname;
      }
      
      return $name;
    }
    
    /**
     * Reflectively creates a new type
     *
     * @param   lang.Type[] arguments
     * @return  lang.XPClass
     * @throws  lang.IllegalStateException if this class is not a generic definition
     * @throws  lang.IllegalArgumentException if number of arguments does not match components
     */
    public function newGenericType($arguments) {
      return new XPClass(new ReflectionClass(self::createGenericType($this, $arguments)));
    }

    /**
     * Returns generic type components
     *
     * @return  string[]
     * @throws  lang.IllegalStateException if this class is not a generic definition
     */
    public function genericComponents() {
      if (!$this->isGenericDefinition()) {
        throw new IllegalStateException('Class '.$this->name.' is not a generic definition');
      }
      $components= array();
      foreach (explode(',', $this->getAnnotation('generic', 'self')) as $name) {
        $components[]= ltrim($name);
      }
      return $components;
    }

    /**
     * Returns whether this class is a generic definition
     *
     * @return  bool
     */
    public function isGenericDefinition() {
      return $this->hasAnnotation('generic', 'self');
    }

    /**
     * Returns generic type definition
     *
     * @return  lang.XPClass
     * @throws  lang.IllegalStateException if this class is not a generic
     */
    public function genericDefinition() {
      if (!($details= self::detailsForClass($this->name))) return NULL;
      if (!isset($details['class'][DETAIL_GENERIC])) {
        throw new IllegalStateException('Class '.$this->name.' is not generic');
      }
      return XPClass::forName($details['class'][DETAIL_GENERIC][0]);
    }

    /**
     * Returns generic type arguments
     *
     * @return  lang.Type[]
     * @throws  lang.IllegalStateException if this class is not a generic
     */
    public function genericArguments() {
      if (!($details= self::detailsForClass($this->name))) return NULL;
      if (!isset($details['class'][DETAIL_GENERIC])) {
        throw new IllegalStateException('Class '.$this->name.' is not generic');
      }
      if (!isset($details['class'][DETAIL_GENERIC][1])) {
        $details['class'][DETAIL_GENERIC][1]= array_map(
          array(xp::reflect('lang.Type'), 'forName'), 
          $details['class'][DETAIL_GENERIC][2]
        );
        unset($details['class'][DETAIL_GENERIC][2]);
      }
      return $details['class'][DETAIL_GENERIC][1];
    }
        
    /**
     * Returns whether this class is generic
     *
     * @return  bool
     */
    public function isGeneric() {
      if (!($details= self::detailsForClass($this->name))) return FALSE;
      return isset($details['class'][DETAIL_GENERIC]);
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

      return $classloader->loadClass((string)$name);
    }

    /**
     * Returns type literal
     *
     * @return  string
     */
    public function literal() {
      return xp::reflect($this->name);
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
        if (isset(xp::$cn[$name])) $ret[]= new self($name);
      }
      return $ret;
    }
  }
?>
