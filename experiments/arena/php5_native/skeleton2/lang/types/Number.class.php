<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The abstract class Number is the superclass of classes representing
   * numbers
   *
   * @model    abstract
   * @purpose  Base class
   */
  class Number extends Object {
    public
      $value = '';

    
    /**
     * Constructor
     *
     * @access  public
     * @param   string value
     */
    public function __construct($value) {
      $this->value= (string)$value;
    }
    
    /**
     * Returns the value of this number as an int.
     *
     * @access  public
     * @return  int
     */
    public function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this number as a float.
     *
     * @access  public
     * @return  int
     */
    public function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this number object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals(&$cmp) {
      return is(get_class($this), $cmp) && $this->value === $cmp->value;
    }
  }
?>
