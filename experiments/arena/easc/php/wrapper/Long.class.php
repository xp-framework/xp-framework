<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The Long class wraps a value of the type long 
   * 
   * Range: -2^63 - (2^63)- 1
   *
   * @purpose  Wrapper
   */
  class Long extends Object {
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
     * Returns the value of this Long as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Long as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Long object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
