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
    
    function addAttribute($key, $value) {
      $this->attributes[$key]= $value;
    }
    
    function getAttributes() {
      return $this->attributes;
    }
  }
?>
