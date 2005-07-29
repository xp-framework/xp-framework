<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * The Integer class wraps a value of the type int 
   * 
   * Range: -2^31 - (2^31)- 1
   *
   * @purpose  Wrapper
   */
  class Integer extends Object {
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
     * Returns the value of this Integer as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Integer as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Integer object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
