<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a header
   *
   * @purpose  Base class for Cookie, Authorization, etc.
   */
  class Header extends Object {
    var 
      $name     = '',
      $value    = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
      parent::__construct();
    }
    
    /**
     * Get header name
     *
     * @access  public
     * @return  string name
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Get header value
     *
     * @access  public
     * @return  string value
     */
    function getValue() {
      return $this->value;
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getName().': '.$this->getValue();
    }
    
    /**
     * Create a header from a string
     *
     * @model   static
     * @access  public
     * @param   string str
     * @return  &peer.Header header object
     */
    function &fromString($str) {
      list($k, $v)= explode(': ', $str, 2);
      return new Header($k, $v);
    }
  }
?>
