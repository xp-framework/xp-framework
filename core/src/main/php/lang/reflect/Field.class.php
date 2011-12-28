<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a class field
   *
   * @test     xp://net.xp_framework.unittest.reflection.FieldsTest
   * @see      xp://lang.XPClass
   * @purpose  Reflection
   */
  class Field extends Object {
    protected
      $accessible = FALSE,
      $_class     = NULL;

    public
      $_reflect   = NULL;

    protected static $SETACCESSIBLE_AVAILABLE;

    static function __static() {
      self::$SETACCESSIBLE_AVAILABLE= method_exists('ReflectionMethod', 'setAccessible');
    }

    /**
     * Constructor
     *
     * @param   string class
     * @param   php.ReflectionProperty reflect
     */    
    public function __construct($class, $reflect) {
      $this->_class= $class;
      $this->_reflect= $reflect;
    }

    /**
     * Get field's name.
     *
     * @return  string
     */
    public function getName() {
      return $this->_reflect->getName();
    }
    
    /**
     * Gets field type
     *
     * @return  string
     */
    public function getType() {
      if ($details= XPClass::detailsForField($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName())) {
        if (isset($details[DETAIL_ANNOTATIONS]['type'])) return $details[DETAIL_ANNOTATIONS]['type'];
      }
      return NULL;
    }

    /**
     * Check whether an annotation exists
     *
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForField($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());

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
     * @return  var
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForField($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());

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
     * Retrieve whether this field has annotations
     *
     * @return  bool
     */
    public function hasAnnotations() {
      $details= XPClass::detailsForField($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
      return $details ? !empty($details[DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of this field's annotations
     *
     * @return  array annotations
     */
    public function getAnnotations() {
      $details= XPClass::detailsForField($this->_reflect->getDeclaringClass()->getName(), $this->_reflect->getName());
      return $details ? $details[DETAIL_ANNOTATIONS] : array();
    }

    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the field represented by this Field object.
     *
     * @return  lang.XPClass
     */
    public function getDeclaringClass() {
      return new XPClass($this->_reflect->getDeclaringClass()->getName());
    }
    
    /**
     * Returns the value of the field represented by this Field, on the 
     * specified object.
     *
     * @param   lang.Object instance
     * @return  var  
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     * @throws  lang.IllegalAccessException in case this field is not public
     */
    public function get($instance) {
      if (NULL !== $instance && !($instance instanceof $this->_class)) {
        throw new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_class),
          xp::typeOf($instance)
        ));
      }

      // Check modifiers. If caller is an instance of this class, allow
      // protected method invocation (which the PHP reflection API does 
      // not).
      $m= $this->_reflect->getModifiers();
      $public= $m & MODIFIER_PUBLIC;
      if (!$public && !$this->accessible) {
        $t= debug_backtrace(0);
        $decl= $this->_reflect->getDeclaringClass()->getName();
        if ($m & MODIFIER_PROTECTED) {
          $allow= $t[1]['class'] === $decl || is_subclass_of($t[1]['class'], $decl);
        } else {
          $allow= $t[1]['class'] === $decl && self::$SETACCESSIBLE_AVAILABLE;
        }
        if (!$allow) {
          throw new IllegalAccessException(sprintf(
            'Cannot read %s %s::$%s from scope %s',
            Modifiers::stringOf($this->getModifiers()),
            $this->_class,
            $this->_reflect->getName(),
            $t[1]['class']
          ));
        }
      }

      // For non-public methods: Use setAccessible() / invokeArgs() combination
      // if possible, resort to __get() / __getStatic() workaround.
      try {
        if ($public) {
          return $this->_reflect->getValue($instance);
        }

        if (self::$SETACCESSIBLE_AVAILABLE) {
          $this->_reflect->setAccessible(TRUE);
          return $this->_reflect->getValue($instance);
        } else if ($m & MODIFIER_STATIC) {
          return call_user_func(array($this->_class, '__getStatic'), "\7".$this->_reflect->getName());
        } else {
          return $instance->__get("\7".$this->_reflect->getName());
        }
      } catch (Throwable $e) {
        throw $e;
      } catch (Exception $e) {
        throw new XPException($e->getMessage());
      }
    }

    /**
     * Changes the value of the field represented by this Field, on the 
     * specified object.
     *
     * @param   lang.Object instance
     * @param   var value
     * @throws  lang.IllegalArgumentException in case the passed object is not an instance of the declaring class
     * @throws  lang.IllegalAccessException in case this field is not public
     */
    public function set($instance, $value) {
      if (NULL !== $instance && !($instance instanceof $this->_class)) {
        throw new IllegalArgumentException(sprintf(
          'Passed argument is not a %s class (%s)',
          xp::nameOf($this->_class),
          xp::typeOf($instance)
        ));
      }
    
      // Check modifiers. If caller is an instance of this class, allow
      // protected method invocation (which the PHP reflection API does 
      // not).
      $m= $this->_reflect->getModifiers();
      $public= $m & MODIFIER_PUBLIC;
      if (!$public && !$this->accessible) {
        $t= debug_backtrace(0);
        $decl= $this->_reflect->getDeclaringClass()->getName();
        if ($m & MODIFIER_PROTECTED) {
          $allow= $t[1]['class'] === $decl || is_subclass_of($t[1]['class'], $decl);
        } else {
          $allow= $t[1]['class'] === $decl && self::$SETACCESSIBLE_AVAILABLE;
        }
        if (!$allow) {
          throw new IllegalAccessException(sprintf(
            'Cannot write %s %s::$%s from scope %s',
            Modifiers::stringOf($this->getModifiers()),
            xp::nameOf($this->_class),
            $this->_reflect->getName(),
            $t[1]['class']
          ));
        }
      }

      // For non-public methods: Use setAccessible() / invokeArgs() combination
      // if possible, resort to __set() / __setStatic() workaround.
      try {
        if ($public) {
          $this->_reflect->setValue($instance, $value);
          return;
        }

        if (self::$SETACCESSIBLE_AVAILABLE) {
          $this->_reflect->setAccessible(TRUE);
          $this->_reflect->setValue($instance, $value);
        } else if ($m & MODIFIER_STATIC) {
          call_user_func(array($this->_class, '__setStatic'), "\7".$this->_reflect->getName(), $value);
        } else {
          $instance->__set("\7".$this->_reflect->getName(), $value);
        }
      } catch (Throwable $e) {
        throw $e;
      } catch (Exception $e) {
        throw new XPException($e->getMessage());
      }
    }

    /**
     * Retrieve this field's modifiers
     *
     * @see     xp://lang.reflect.Modifiers
     * @return  int
     */    
    public function getModifiers() {
      return $this->_reflect->getModifiers();
    }

    /**
     * Sets whether this routine should be accessible from anywhere, 
     * regardless of its visibility level.
     *
     * @param   bool flag
     * @return  lang.reflect.Routine this
     */
    public function setAccessible($flag) {
      if (!self::$SETACCESSIBLE_AVAILABLE && $this->_reflect->isPrivate()) {
        throw new IllegalAccessException('Cannot make private fields accessible');
      }
      $this->accessible= $flag;
      return $this;
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
      return 'F['.$this->_reflect->getDeclaringClass().$this->_reflect->getName();
    }
    
    /**
     * Creates a string representation of this field
     *
     * @return  string
     */
    public function toString() {
      $t= $this->getType();
      return sprintf(
        '%s%s %s::$%s',
        Modifiers::stringOf($this->getModifiers()),
        $t ? ' '.$t : '',
        $this->getDeclaringClass()->getName(),
        $this->getName()
      );
    }
  }
?>
