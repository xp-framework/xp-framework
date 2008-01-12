<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Argument', 'lang.reflect.TargetInvocationException');

  /**
   * Base class for methods and constructors. Note that the methods provided
   * in this class (except for getName()) are implemented using a tokenizer
   * on the class files, gathering its information from the API docs.
   *
   * This, of course, will not be as fast as if the details were provided by
   * PHP itself and will also rely on the API docs being consistent and 
   * correct.
   *
   * @test     xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Reflection
   */
  class Routine extends Object {
    protected
      $_class   = NULL;

    public 
      $_reflect = NULL;

    /**
     * Constructor
     *
     * @param   string class
     * @param   php.ReflectionMethod reflect
     */    
    public function __construct($class, $reflect) {
      $this->_class= $class;
      $this->_reflect= $reflect;
    }
    
    /**
     * Get routine's name.
     *
     * @return  string
     */
    public function getName() {
      return $this->_reflect->getName();
    }
    
    /**
     * Retrieve this method's modifiers
     *
     * @see     xp://lang.reflect.Modifiers
     * @return  int
     */    
    public function getModifiers() {
    
      // Note: ReflectionMethod::getModifiers() returns whatever PHP reflection 
      // returns, but the numeric value changed since 5.0.0 as the zend_function
      // struct's fn_flags now contains not only ZEND_ACC_(PPP, STATIC, FINAL,
      // ABSTRACT) but also some internal information about how this method needs
      // to be called.
      //
      // == List of fn_flags we don't want to return from this method ==
      // #define ZEND_ACC_IMPLEMENTED_ABSTRACT  0x08
      // #define ZEND_ACC_IMPLICIT_PUBLIC       0x1000
      // #define ZEND_ACC_CTOR                  0x2000
      // #define ZEND_ACC_DTOR                  0x4000
      // #define ZEND_ACC_CLONE                 0x8000
      // #define ZEND_ACC_ALLOW_STATIC          0x10000
      // #define ZEND_ACC_SHADOW                0x20000
      // #define ZEND_ACC_DEPRECATED            0x40000
      // ==
      return $this->_reflect->getModifiers() & ~0x7f008;
    }
    
    /**
     * Retrieve this method's arguments
     *
     * @return  lang.reflect.Argument[]
     */
    public function getArguments() {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      $r= array();

      foreach ($this->_reflect->getParameters() as $pos => $param) {
        $optional= $param->isOptional();
        $r[]= new Argument(
          $param->getName(),
          array(    // 0 = Declared in apidoc, 1 = Type hint
            ltrim(@$details[DETAIL_ARGUMENTS][$pos], '&'),
            $param->isArray() ? 'array' : ($param->getClass() ? xp::nameOf($param->getClass()->getName()) : NULL)
          ),
          $optional,
          $optional ? $param->getDefaultValue() : NULL
        );
      }
      return $r;
    }

    /**
     * Retrieve one of this method's argument by its position
     *
     * @param   int pos
     * @return  lang.reflect.Argument
     */
    public function getArgument($pos) {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      $param= $this->_reflect->getParameters();
      if (!isset($param[$pos])) return NULL;

      $optional= $param[$pos]->isOptional();
      return new Argument(
        $param[$pos]->getName(),
          array(    // 0 = Declared in apidoc, 1 = Type hint
            ltrim(@$details[DETAIL_ARGUMENTS][$pos], '&'),
            $param[$pos]->isArray() ? 'array' : ($param[$pos]->getClass() ? xp::nameOf($param[$pos]->getClass()->getName()) : NULL)
          ),
        $optional,
        $optional ? $param[$pos]->getDefaultValue() : NULL
      );
    }

    /**
     * Retrieve how many arguments this method accepts (including optional ones)
     *
     * @return  int
     */
    public function numArguments() {
      return $this->_reflect->getNumberOfParameters();
    }

    /**
     * Retrieve return type
     *
     * @return  string
     */
    public function getReturnType() {
      if (!($details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName()))) return NULL;
      return ltrim($details[DETAIL_RETURNS], '&');
    }

    /**
     * Retrieve exception names
     *
     * @return  string[]
     */
    public function getExceptionNames() {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      return $details ? $details[DETAIL_THROWS] : array();
    }

    /**
     * Retrieve exception types
     *
     * @return  lang.XPClass[]
     */
    public function getExceptionTypes() {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      return $details ? array_map(array('XPClass', 'forName'), $details[DETAIL_THROWS]) : array();
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * @return  lang.XPClass
     */
    public function getDeclaringClass() {
      return new XPClass($this->_reflect->getDeclaringClass()->getName());
    }
    
    /**
     * Retrieves the api doc comment for this method. Returns NULL if
     * no documentation is present.
     *
     * @return  string
     */
    public function getComment() {
      if (!($details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName()))) return NULL;
      return $details[DETAIL_COMMENT];
    }
    
    /**
     * Check whether an annotation exists
     *
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());

      return $details && ($key 
        ? array_key_exists($key, (array)@$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, (array)@$details[DETAIL_ANNOTATIONS])
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
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());

      if (!$details || !($key 
        ? array_key_exists($key, @$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details[DETAIL_ANNOTATIONS])
      )) return raise(
        'lang.ElementNotFoundException', 
        'Annotation "'.$name.($key ? '.'.$key : '').'" does not exist'
      );

      return ($key 
        ? $details[DETAIL_ANNOTATIONS][$name][$key] 
        : $details[DETAIL_ANNOTATIONS][$name]
      );
    }

    /**
     * Retrieve whether a method has annotations
     *
     * @return  bool
     */
    public function hasAnnotations() {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      return $details ? !empty($details[DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of a method's annotations
     *
     * @return  array annotations
     */
    public function getAnnotations() {
      $details= XPClass::detailsForMethod($this->_class, $this->_reflect->getName());
      return $details ? $details[DETAIL_ANNOTATIONS] : array();
    }
    
    /**
     * Returns whether an object is equal to this routine
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $cmp->_reflect->getName() === $this->_reflect->getName() &&
        $cmp->getDeclaringClass()->equals($this->getDeclaringClass())
      );
    }
    
    /**
     * Retrieve string representation. Examples:
     *
     * <pre>
     *   public lang.XPClass getClass()
     *   public static util.Date now()
     *   public open(string $mode) throws io.FileNotFoundException, io.IOException
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      $args= '';
      for ($arguments= $this->getArguments(), $i= 0, $s= sizeof($arguments); $i < $s; $i++) {
        if ($arguments[$i]->isOptional()) {
          $args.= ', ['.$arguments[$i]->getType().' $'.$arguments[$i]->getName().'= '.$arguments[$i]->getDefault().']';
        } else {
          $args.= ', '.$arguments[$i]->getType().' $'.$arguments[$i]->getName();
        }
      }
      if ($exceptions= $this->getExceptionNames()) {
        $throws= ' throws '.implode(', ', $exceptions);
      } else {
        $throws= '';
      }
      return sprintf(
        '%s %s %s(%s)%s',
        Modifiers::stringOf($this->getModifiers()),
        $this->getReturnType(),
        $this->getName(),
        substr($args, 2),
        $throws
      );
    }
  }
?>
