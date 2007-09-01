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
    public 
      $name     = '',
      $value    = '';
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   string value
     */
    public function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
      
    }
    
    /**
     * Get header name
     *
     * @return  string name
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Get header value
     *
     * @return  string value
     */
    public function getValue() {
      return $this->value;
    }
    
    /**
     * Get header value representation
     *
     * @return  string value
     */
    public function getValueRepresentation() {
      return $this->value;
    }
    
    /**
     * Create string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getName().': '.$this->getValueRepresentation();
    }
    
    /**
     * Create a header from a string
     *
     * @param   string str
     * @return  peer.Header header object
     */
    public static function fromString($str) {
      list($k, $v)= explode(': ', $str, 2);
      return new self($k, $v);
    }
  }
?>
