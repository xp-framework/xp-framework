<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Parameter', 'lang.reflect.TargetInvocationException');

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
     * Returns this method's parameters
     *
     * @return  lang.reflect.Parameter[]
     */
    public function getParameters() {
      $r= array();
      foreach ($this->_reflect->getParameters() as $offset => $param) {
        $r[]= new lang·reflect·Parameter($param, array($this->_class, $this->_reflect->getName(), $offset));
      }
      return $r;
    }

    /**
     * Retrieve one of this method's parameters by its offset
     *
     * @param   int offset
     * @return  lang.reflect.Parameter or NULL if it does not exist
     */
    public function getParameter($offset) {
      $list= $this->_reflect->getParameters();
      return isset($list[$offset]) 
        ? new lang·reflect·Parameter($list[$offset], array($this->_class, $this->_reflect->getName(), $offset))
        : NULL
      ;
    }
    
    /**
     * Retrieve how many parameters this method declares (including optional 
     * ones)
     *
     * @return  int
     */
    public function numParameters() {
      return $this->_reflect->getNumberOfParameters();
    }

    /**
     * Retrieve return type
     *
     * @return  string
     */
    public function getReturnType() {
      if (!($details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName()))) return Type::$ANY;
      return Type::forName(ltrim($details[DETAIL_RETURNS], '&'));
    }

    /**
     * Retrieve return type name
     *
     * @return  string
     */
    public function getReturnTypeName() {
      if (!($details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName()))) return NULL;
      return ltrim($details[DETAIL_RETURNS], '&');
    }

    /**
     * Retrieve exception names
     *
     * @return  string[]
     */
    public function getExceptionNames() {
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
      return $details ? $details[DETAIL_THROWS] : array();
    }

    /**
     * Retrieve exception types
     *
     * @return  lang.XPClass[]
     */
    public function getExceptionTypes() {
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
      return $details ? array_map(array(xp::reflect('lang.XPClass'), 'forName'), $details[DETAIL_THROWS]) : array();
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * @return  lang.XPClass
     */
    public function getDeclaringClass() {
      return new XPClass($this->_reflect->getDeclaringClass());
    }
    
    /**
     * Retrieves the api doc comment for this method. Returns NULL if
     * no documentation is present.
     *
     * @return  string
     */
    public function getComment() {
      if (!($details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName()))) return NULL;
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
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());

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
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());

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
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
      return $details ? !empty($details[DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of a method's annotations
     *
     * @return  array annotations
     */
    public function getAnnotations() {
      $details= XPClass::detailsForMethod($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
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
     * Returns a hashcode for this routine
     *
     * @return  string
     */
    public function hashCode() {
      return 'R['.$this->_reflect->getDeclaringClass().$this->_reflect->getName();
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
      $signature= '';
      foreach ($this->getParameters() as $param) {
        if ($param->isOptional()) {
          $signature.= ', ['.$param->getTypeName().' $'.$param->getName().'= '.xp::stringOf($param->getDefaultValue()).']';
        } else {
          $signature.= ', '.$param->getTypeName().' $'.$param->getName();
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
        $this->getReturnTypeName(),
        $this->getName(),
        substr($signature, 2),
        $throws
      );
    }
  }
?>
