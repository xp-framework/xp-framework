<?php
/* This class is part of the XP framework
 *
 * $Id: QName.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace xml;

  /**
   * Qualified name
   *
   * Example:
   * <code>
   *   new QName('http://schemas.xmlsoap.org/ws/2003/03/addressing', 'To', 'wsa');
   * </code>
   *
   * Result:
   * <pre>
   *   <wsa:To xmlns:wsa="http://schemas.xmlsoap.org/ws/2003/03/addressing"/>
   * </pre>
   *
   * @purpose  XML Namespaces
   */
  class QName extends lang::Object {
    public
      $namespace    = '',
      $localpart    = '',
      $prefix       = '';
    
    /**
     * Constructor
     *
     * @param   string namespace
     * @param   string localpart
     * @param   string prefix default NULL
     */
    public function __construct($namespace, $localpart, $prefix= ) {
      $this->namespace= $namespace;
      $this->localpart= $localpart;
      $this->prefix= $prefix;
    }
    
    /**
     * Returns a string representation
     *
     * @return  string
     */
    public function toString() {
      return ltrim($this->namespace.'/'.$this->localpart, '/');
    }
  }
?>
