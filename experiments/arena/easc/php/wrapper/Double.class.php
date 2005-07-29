<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('wrapper.Number');

  /**
   * The Double class wraps a value of the type double
   *
   * @purpose  Wrapper
   */
  class Double extends Number {
    
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
     * Returns the value of this Double as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Double as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Double object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
