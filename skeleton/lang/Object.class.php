<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Class Object is the root of the class hierarchy. Every class has 
   * Object as a superclass. 
   *
   * @purpose  Base class for all others
   */
  class Object {
  
    /**
     * Constructor wrapper 
     * 
     * @access  private
     */
    function Object() {
      $args= func_get_args();
      call_user_func_array(
        array(&$this, '__construct'),
        $args
      );
    }

    /**
     * Constructor. Supports the array syntax, where an associative
     * array is passed to the constructor, the keys being the member
     * variables and the values the member's values.
     *
     * @access  public
     */
    function __construct($params= NULL) {
      if (is_array($params)) {
        foreach ($params as $key=> $val) $this->$key= $val;
      }
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
     * <pre>Warning: Deprecated! Use getClassName() instead</pre>
     *
     * @return  string fully qualified class name
     * @see     xp://lang.Object#getClassName
     */
    function getName() {
      return $GLOBALS['php_class_names'][get_class($this)];
    }

    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @return  string fully qualified class name
     */
    function getClassName() {
      return $GLOBALS['php_class_names'][get_class($this)];
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
     * Creates a string representation of this object. In general, the toString 
     * method returns a string that "textually represents" this object. The result 
     * should be a concise but informative representation that is easy for a 
     * person to read. It is recommended that all subclasses override this method.
     * 
     * Per default, this method returns:
     * <xmp>
     *   [fully-qualified-class-name]@[serialized-object]
     * </xmp>
     * 
     * Example:
     * <xmp>
     *   de.sitten-polizei.Test@O:4:"test":1:{s:4:"test";N;}
     * </xmp>
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'@'.var_export($this, 1);
    }
  }
?>
