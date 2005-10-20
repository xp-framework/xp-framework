<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Qualified name
   *
   * @purpose  Wrapper
   */
  class QName extends Object {
    var
      $namespace    = '',
      $localpart    = '',
      $prefix       = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string namespace
     * @param   string localpart
     */
    function __construct($namespace, $localpart, $prefix= NULL) {
      $this->namespace= $namespace;
      $this->localpart= $localpart;
      $this->prefix= $prefix;
    }
    
    /**
     * Returns a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return ltrim($this->namespace.'/'.$this->localpart, '/');
    }
  }
?>
