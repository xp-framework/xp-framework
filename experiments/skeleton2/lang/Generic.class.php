<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generic interface. All XP classes implement this interface
   *
   * @see      xp://lang.Object
   * @see      xp://lang.Throwable
   * @purpose  purpose
   */
  interface Generic {

    /**
     * Returns a hashcode for this object
     *
     * @access  public
     * @return  string
     */
    public function hashCode();
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Generic cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals(Generic $cmp);
    
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     *
     * This is a shorthand for the following:
     * <code>
     *   $name= $instance->getClass()->getName();
     * </code>
     * 
     * @access  public
     * @return  string fully qualified class name
     */
    public function getClassName();

    /**
     * Returns the runtime class of an object.
     *
     * @access  public
     * @return  &lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public function getClass();

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
    public function toString();

    /**
     * Wrapper for PHP's builtin cast mechanism
     *
     * @see     xp://lang.Object#toString
     * @access  public
     * @return  string
     */
    public function __toString();
  }
?>
