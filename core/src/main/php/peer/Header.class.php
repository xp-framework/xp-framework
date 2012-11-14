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
      return $this->getValue();
    }
    
    /**
     * Create string representation
     *
     * TBD: Why two methods returning the same?
     *      Do I really wish to use eventually different values for comparison and toString output?
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

    /**
     * Indicates whether the header to compare equals this header.
     *
     * @param   peer.Header cmp
     * @return  bool TRUE if headers are equal
     */
    public function equals($cmp) {
      return ($cmp instanceof self) && ($this->getName() === $cmp->getName()) && ($this->getValue() === $cmp->getValue());
    }
    
    /**
     * Will return if this header is a valid request header
     *
     * @return bool TRUE
     */
    public function isRequestHeader() {
      return TRUE;
    }

    /**
     * Will return if this header is a valid response header
     *
     * @return bool TRUE
     */
    public function isResponseHeader() {
      return TRUE;
    }
    
    /**
     * By default mark headers as not unique
     *
     * @return bool FALSE
     */
    public function isUnique() {
      return FALSE;
    }
  }
?>
