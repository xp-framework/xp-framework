<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The Byte class wraps a value of the type byte 
   * 
   * Range: -2^7 - (2^7)- 1
   *
   * @purpose  Wrapper
   */
  class Byte extends Object {
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
     * Returns the value of this Byte as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Byte as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Byte object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
