<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Wraps LDAP entry
   *
   * @see 
   */
  class LDAPEntry extends Object {
    var
      $dn=          '',
      $attributes=  array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string dn default NULL "distinct name"
     */
    function __construct($dn= NULL, $attrs= array()) {
      $this->dn= $dn;
      $this->attributes= $attrs;
      parent::__construct();
    }
    
    function setDN($dn) {
      $this->dn= $dn;
    }
    
    function getDN() {
      return $this->dn;
    }
    
    function setAttribute($key, $value) {
      $this->attributes[$key]= $value;
    }
    
    function getAttribute($key, $idx= -1) {
      if (-1 != $idx) return $this->attributes[$key][$idx];
      return $this->attributes[$key];
    }
    
    function getAttributes() {
      return $this->attributes;
    }
  }
?>
