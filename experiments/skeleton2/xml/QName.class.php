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
    public
      $namespace    = '',
      $localpart    = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string namespace
     * @param   string localpart
     */
    public function __construct($namespace, $localpart) {
      
      $this->namespace= $namespace;
      $this->localpart= $localpart;
    }
    
    /**
     * Returns a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return ltrim($this->namespace.'/'.$this->localpart, '/');
    }
  }
?>
