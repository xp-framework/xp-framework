/* This class is part of the XP framework
 *
 * $Id$ <?php
 */

package lang {

 
  /**
   * Class Object is the root of the class hierarchy. Every class has 
   * Object as a superclass. 
   *
   * @purpose  Base class for all others
   */
  class Object {
    public $__id;
    
    /**
     * Constructor wrapper 
     * 
     * @access  private
     */
    public function Object() {
      $this->__id= microtime();
      if (method_exists($this, '__destruct'))
        register_shutdown_function(array(&$this, '__destruct'));
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
    public function hashCode() {
      return $this->__id;
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals(&$cmp) {
      return $this === $cmp;
    }
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @access  public
     * @return  string fully qualified class name
     */
    public function getClassName() {
      return xp::nameOf(get_class($this));
    }

    /**
     * Returns the runtime class of an object.
     *
     * @access  public
     * @return  &lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public function &getClass() {
      $c= &new lang~XPClass($this);
      return $c;
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
     * lang.Object@class object {
     *   var $__id = '0.06823200 1062749651';
     * }
     * </xmp>
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.var_export($this, 1);
    }
  }
}
