<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('wrapper.Number');

  /**
   * The Float class wraps a value of the type float
   *
   * @purpose  Wrapper
   */
  class Float extends Number {
    
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
     * Returns the value of this Float as an int.
     *
     * @access  public
     * @return  int
     */
    function intValue() {
      return (int)$this->value;
    }

    /**
     * Returns the value of this Float as a float.
     *
     * @access  public
     * @return  int
     */
    function floatValue() {
      return (float)$this->value;
    }
    
    /**
     * Returns a string representation of this Float object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->value.')';
    }
  }
?>
