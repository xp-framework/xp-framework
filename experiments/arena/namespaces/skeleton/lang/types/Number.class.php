<?php
/* This class is part of the XP framework
 *
 * $Id: Number.class.php 10586 2007-06-08 16:12:43Z friebe $ 
 */

  namespace lang::types;

  /**
   * The abstract class Number is the superclass of classes representing
   * numbers
   *
   * @purpose  Base class
   */
  class Number extends lang::Object {
    public
      $value = '';

    
    /**
     * Constructor
     *
     * @param   string value
     */
    public function __construct($value) {
      $this->value= (string)$value;
    }
    
    /**
     * Returns the value of this number as an int.
     *
     * @return  int
     */
    public function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this number as a float.
     *
     * @return  float
     */
    public function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a hashcode for this number
     *
     * @return  string
     */
    public function hashCode() {
      return $this->value;
    }

    /**
     * Returns a string representation of this number object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
    
    /**
     * Indicates whether some other object is "equal to" this one.
     *
     * @param   lang.Object cmp
     * @return  bool TRUE if the compared object is equal to this object
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->value === $cmp->value;
    }
  }
?>
