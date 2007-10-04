<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Wraps LDAP entry
   *
   * @purpose  Represent a single entry
   * @see      xp://peer.ldap.LDAPSearchResult
   * @see      xp://peer.ldap.LDAPClient
   * @test     xp://net.xp_framework.unittest.peer.LDAPEntryTest
   */
  class LDAPEntry extends Object {
    public
      $dn=          '',
      $attributes=  array();
    
    protected
      $_ans=        array();
      
    /**
     * Constructor
     *
     * @param   string dn default NULL "distinct name"
     * @param   mixed[] attrs
     */
    public function __construct($dn= NULL, $attrs= array()) {
      $this->dn= $dn;
      $this->attributes= array_change_key_case($attrs, CASE_LOWER);
      $this->_ans= array_combine(array_keys($this->attributes), array_keys($attrs));
    }

    /**
     * Decode entries (recursively, if needed)
     *
     * @param   mixed v
     * @return  string decoded entry
     */
    protected function _decode($v) {
      if (is_array($v)) for ($i= 0, $m= sizeof($v); $i < $m; $i++) {
        $v[$i]= $this->_decode($v[$i]);
        return $v;
      }
      return utf8_decode($v);
    }
        
    /**
     * Creates an LDAP from the raw return data of PHP's ldap_* functions
     * Also performs decoding on the attributes.
     *
     * @param   resource handle ldap connection
     * @param   resource res ldap result resource
     * @return  peer.ldap.LDAPEntry object
     */
    public static function fromData($handle, $res) {
      $data= ldap_get_attributes($handle, $res);
      $e= new LDAPEntry(ldap_get_dn($handle, $res));

      foreach ($data as $key => $value) {
        if ('count' == $key || is_int($key)) continue;
        $e->_ans[strtolower($key)]= $key;
        
        if (is_array($value)) {
          $e->attributes[strtolower($key)]= array_map(array($e, '_decode'), $value);
        } else {
          $e->attributes[strtolower($key)]= $e->_decode($value);
        }
        unset($e->attributes[strtolower($key)]['count']);
      }

      return $e;
    }
    
    /**
     * Set this entry's DN (distinct name)
     *
     * @param   string dn
     */
    public function setDN($dn) {
      $this->dn= $dn;
    }
    
    /**
     * Retrieve this entry's DN (distinct name)
     *
     * @return  string DN
     */
    public function getDN() {
      return $this->dn;
    }

    /**
     * Set attribute
     *
     * @param   string key
     * @param   mixed value
     */
    public function setAttribute($key, $value) {
      $this->_ans[strtolower($key)]= $key;
      $this->attributes[strtolower($key)]= (array)$value;
    }
    
    /**
     * Retrieve an attribute - an offset may be supplied to define
     * the values offset within the attribute. If -1 (the default)
     * is supplied, an array of attribute values is returned.
     *
     * Note: If the value does not exist, NULL is returned
     *
     * @param   string key
     * @param   int idx default -1
     * @return  mixed attribute
     */
    public function getAttribute($key, $idx= -1) {
      return (($idx >= 0)
        ? (isset($this->attributes[strtolower($key)][$idx]) ? $this->attributes[strtolower($key)][$idx] : NULL)
        : (isset($this->attributes[strtolower($key)]) ? $this->attributes[strtolower($key)] : NULL)
      );
    }
    
    /**
     * Retrieve all attributes
     *
     * @return  array
     */
    public function getAttributes() {
      return $this->attributes;
    }
    
    /**
     * Retrieve whether this entry is of a given object class.
     *
     * Note: The given objectClass is treated case-sensitive!
     *
     * @param   string objectClass
     * @return  bool
     */
    public function isA($objectClass) {
      return in_array($objectClass, $this->attributes['objectclass']);
    }

    /**
     * Retrieve a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= sprintf("%s@DN(%s){\n", $this->getClassName(), $this->getDN());
      foreach ($this->attributes as $name => $attr) {
        $s.= sprintf("  [%-20s] %s\n", $this->_ans[$name], implode(', ', $attr));
      }
      return $s."}\n";
    }
  }
?>
