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

    /**
     * Decode entries (recursively, if needed)
     *
     * @access  private
     * @param   &mixed v
     * @return  string decoded entry
     */
    function _decode(&$v) {
      if (is_array($v)) for ($i= 0, $m= sizeof($v); $i < $m; $i++) {
        $v[$i]= $this->_decode($v[$i]);
        return $v;
      }
      return utf8_decode($v);
    }
        
    /**
     * (Insert method's description here)
     *
     * @access  static
     * @param   
     * @return  
     */
    function &fromData(&$data) {
      $e= &new LDAPEntry($data['dn']);
      foreach (array_keys($data) as $key) {
        if ('count' == $key || is_int($key)) continue;
        
        $e->attributes[$key]= (is_array($data[$key])
          ? array_map(array(&$e, '_decode'), $data[$key])
          : $e->_decode($data[$key])
        );
        unset($e->attributes[$key]['count']);
      }
      
      return $e;
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
