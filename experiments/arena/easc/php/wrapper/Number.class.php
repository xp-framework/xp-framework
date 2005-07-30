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
    var
      $value = '';

    
    /**
     * Constructor
     *
     * @access  public
     * @param   string value
     */
    function __construct($value) {
      $this->value= (string)$value;
    }
    
    /**
     * Returns the value of this number as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this number as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this number object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
