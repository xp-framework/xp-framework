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
      $this->__id= uniqid('', TRUE);
    }

    /**
     * Static method handler
     *
     */
    public static function __callStatic($name, $args) {
      $t= debug_backtrace();

      // Get self
      $i= 1; $s= sizeof($t);
      while (!isset($t[$i]['class']) && $i++ < $s) { }
      $self= $t[$i]['class'];

      // This is a bug in PHP 5.3.3, parent::method() will always invoke __callStatic()
      // Check up to the next level if we can find an object, this is an indicator that 
      // this situations is occurring. In other PHP version, we don't have an object here
      // in any case reproducable at the time of writing, thus saving version_compare()
      if (isset($t[$i+ 1]['object'])) {
        $instance= $t[$i+ 1]['object'];

        // Get scope
        $i++;
        while (!isset($t[$i]['class']) && $i++ < $s) { }
        $scope= isset($t[$i]['class']) ? $t[$i]['class'] : NULL;

        if (NULL != $scope && isset(xp::$ext[$scope])) {
          foreach (xp::$ext[$scope] as $type => $class) {
            if (!$instance instanceof $type || !method_exists($class, $name)) continue;
            array_unshift($args, $instance);
            return call_user_func_array(array($class, $name), $args);
          }
        }
        throw new Error('Call to undefined method '.xp::nameOf($self).'::'.$name.'() from scope '.xp::nameOf($scope));
      }

      if ("\7" === $name{0}) {
        return call_user_func_array(array($self, substr($name, 1)), $args);
      }
      throw new Error('Call to undefined static method '.xp::nameOf($self).'::'.$name.'()');
    }

    /**
     * Field read handler
     *
     */
    public function __get($name) {
      return NULL;
    }

    /**
     * Field write handler
     *
     */
    public function __set($name, $value) {
      $this->{$name}= $value;
    }
    
    /**
     * Method handler
     *
     */
    public function __call($name, $args) {
      if ("\7" === $name{0}) {
        return call_user_func_array(array($this, substr($name, 1)), $args);
      }
      $t= debug_backtrace();

      // Get self
      $i= 1; $s= sizeof($t);
      while (!isset($t[$i]['class']) && $i++ < $s) { }
      $self= $t[$i]['class'];

      // Get scope
      $i++;
      while (!isset($t[$i]['class']) && $i++ < $s) { }
      $scope= isset($t[$i]['class']) ? $t[$i]['class'] : NULL;

      if (NULL != $scope && isset(xp::$ext[$scope])) {
        foreach (xp::$ext[$scope] as $type => $class) {
          if (!$this instanceof $type || !method_exists($class, $name)) continue;
          array_unshift($args, $this);
          return call_user_func_array(array($class, $name), $args);
        }
      }
      throw new Error('Call to undefined method '.xp::nameOf($self).'::'.$name.'() from scope '.xp::nameOf($scope));
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      if (!$this->__id) $this->__id= uniqid('', TRUE);
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
      if (!$this->__id) $this->__id= uniqid('', TRUE);
      if (!$cmp->__id) $cmp->__id= uniqid('', TRUE);
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
      if (!$this->__id) $this->__id= uniqid('', TRUE);
      return xp::stringOf($this);
    }
  }
?>
