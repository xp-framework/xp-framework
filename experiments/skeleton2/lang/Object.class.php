<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

namespace lang {
 
  /**
   * Class Object is the root of the class hierarchy. Every class has 
   * Object as a superclass. 
   *
   * @purpose  Base class for all others
   */
  class Object {
  
    /** 
     * Returns the fully qualified class name for this class 
     * (e.g. "io.File")
     * 
     * @return  string fully qualified class name
     */
    public function getClassName() {
      return xp::registry::$names[get_class($this)];
    }

    /**
     * Returns the runtime class of an object.
     *
     * @access  public
     * @return  lang.XPClass runtime class
     * @see     xp://lang.XPClass
     */
    public function getClass() {
      return lang::XPClass::forInstance($this);
    }
    
    /**
     * Returns whether the given object is equal to this
     *
     * @access  public
     * @param   lang.Object object
     * @return  bool TRUE if bothe objects are equal (the "same" object)
     */
    public function equals($object) {
      return $object === $this;
    }

    /**
     * Creates a string representation of this object. In general, the toString 
     * method returns a string that "textually represents" this object. The result 
     * should be a concise but informative representation that is easy for a 
     * person to read. It is recommended that all subclasses override this method.
     * 
     * Per default, this method returns:
     * <xmp>
     *   [fully-qualified-class-name]@[exported-object]
     * </xmp>
     * 
     * Example:
     * <xmp>
     *   de.sitten-polizei.Test@class Test {
     * </xmp>
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return self::getClassName().'@'.var_export($this, 1);
    }
  }
}
?>
