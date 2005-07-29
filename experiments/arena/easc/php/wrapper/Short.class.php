<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The Short class wraps a value of the type short 
   * 
   * Range: -2^15 - (2^15)- 1
   *
   * @purpose  Wrapper
   */
  class Short extends Object {
    var
      $value  = '';
    
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
     * Returns the value of this Short as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Short as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Short object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
