<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for all enumerations
   *
   * @purpose  Enumeration
   */
  class Enum {
    var 
      $__id,
      $ordinal  = 0,
      $value    = NULL,
      $name     = '';

    /**
     * Constructor wrapper 
     * 
     * @access  private
     */
    function Enum($name, $value) {
      $this->__id= microtime();
      $this->ordinal= constant($name);
      $this->value= $value;
      $this->name= $name;
      if (!method_exists($this, '__construct')) return;
      $args= func_get_args();
      call_user_func_array(array(&$this, '__construct'), $args);
    }

    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    function equals(&$cmp) {
      return $this === $cmp;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      unset($this);
    }

    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @access  public
     * @return  string fully qualified class name
     */
    function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @access  public
     * @return  &lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    function &getClass() {
      return new XPClass($this);
    }

    /**
     * Creates a string representation of this enumeration.
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'@'.$this->ordinal.' {'.$this->name.'}';
    }
  }
?>
