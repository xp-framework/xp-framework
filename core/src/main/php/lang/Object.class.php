<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.Generic');
 
  /**
   * Class Object is the root of the class hierarchy. Every class has 
   * Object as a superclass. 
   *
   * @test     xp://net.xp_framework.unittest.core.ObjectTest
   * @purpose  Base class for all others
   */
  class Object implements Generic {
    public $__id;
    
    /**
     * Cloning handler
     *
     */
    public function __clone() {
      if (!$this->__id) $this->__id= microtime();
      $this->__id= microtime();
    }

    /**
     * Static field read handler
     *
     */
    public static function __getStatic($name) {
      if ("\7" === $name{0}) {
        $t= debug_backtrace();
        return eval('return '.$t[1]['args'][0][0].'::$'.substr($name, 1).';');
      }
      return NULL;
    }

    /**
     * Static field read handler
     *
     */
    public static function __setStatic($name, $value) {
      if ("\7" === $name{0}) {
        $t= debug_backtrace();
        eval($t[1]['args'][0][0].'::$'.substr($name, 1).'= $value;');
        return;
      }
    }

    /**
     * Static method handler
     *
     */
    public static function __callStatic($name, $args) {
      if ("\7" === $name{0}) {
        $t= debug_backtrace();
        return call_user_func_array(array($t[1]['args'][0][0], substr($name, 1)), $args);
      }
      $t= debug_backtrace();
      throw new Error('Call to undefined method '.$t[1]['class'].'::'.$name);
    }

    /**
     * Field read handler
     *
     */
    public function __get($name) {
      if ("\7" === $name{0}) {
        return $this->{substr($name, 1)};
      }
      return NULL;
    }

    /**
     * Field write handler
     *
     */
    public function __set($name, $value) {
      if ("\7" === $name{0}) {
        $this->{substr($name, 1)}= $value;
        return;
      }
      $this->{$name}= $value;
    }
    
    /**
     * Method handler
     *
     */
    public function __call($name, $args) {
      if ("\7" === $name{0}) {
        return call_user_func_array(array($this, substr($name, 1)), $args);
      } else if (FALSE !== ($p= strpos($name, '<'))) {
        array_unshift($args, Type::forName(substr($name, $p+ 1, -1)));
        return call_user_func_array(array($this, substr($name, 0, $p).'��'), $args);
      }
      $t= debug_backtrace();
      $i= 1; $s= sizeof($t);
      while ($i++ < $s && !isset($t[$i]['class'])) { }
      $scope= $t[$i]['class'];
      if (isset(xp::$registry['ext'][$scope])) {
        foreach (xp::$registry['ext'][$scope] as $type => $class) {
          if (!$this instanceof $type || !method_exists($class, $name)) continue;
          array_unshift($args, $this);
          return call_user_func_array(array($class, $name), $args);
        }
      }
      throw new Error('Call to undefined method '.$this->getClassName().'::'.$name.'() from scope '.xp::nameOf($scope));
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      if (!$this->__id) $this->__id= microtime();
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @param   lang.Generic cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals($cmp) {
      if (!$cmp instanceof Generic) return FALSE;
      if (!$this->__id) $this->__id= microtime();
      if (!$cmp->__id) $cmp->__id= microtime();
      return $this === $cmp;
    }
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @return  string fully qualified class name
     */
    public function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @return  lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public function getClass() {
      return new XPClass($this);
    }
    
    /**
     * Creates a string representation of this object. In general, the toString 
     * method returns a string that "textually represents" this object. The result 
     * should be a concise but informative representation that is easy for a 
     * person to read. It is recommended that all subclasses override this method.
     * 
     * Per default, this method returns:
     * <xmp>
     *   [fully-qualified-class-name] '{' [members-and-value-list] '}'
     * </xmp>
     * 
     * Example:
     * <xmp>
     *   lang.Object {
     *     __id => "0.43080500 1158148350"
     *   }
     * </xmp>
     *
     * @return  string
     */
    public function toString() {
      if (!$this->__id) $this->__id= microtime();
      return xp::stringOf($this);
    }
  }
?>
