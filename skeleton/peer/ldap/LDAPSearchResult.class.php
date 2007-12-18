<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ldap.LDAPEntry');

  /**
   * Wraps ldap search results
   *
   * @see      php://ldap_get_entries
   * @test     xp://net.xp_framework.unittest.peer.LDAPResultTest
   */
  class LDAPSearchResult extends Object {
    public
      $size     = NULL;

    protected
      $_hdl     = NULL,
      $_res     = NULL,
      $entry    = NULL,
      $entries  = array();

    /**
     * Constructor
     *
     * @param   resource hdl ldap connection
     * @param   resource res ldap result resource
     */
    public function __construct($hdl, $res) {
      $this->_hdl= $hdl;
      $this->_res= $res;
      $this->size= ldap_count_entries($this->_hdl, $this->_res);
    }
    
    /**
     * Returns number of found elements
     *
     * @return  int
     */
    public function numEntries() {
      return $this->size;
    }
    
    /**
     * Gets first entry
     *
     * @return  peer.ldap.LDAPEntry or FALSE if none exists by this offset
     * @throws  peer.ldap.LDAPException in case of a read error
     */
    public function getFirstEntry() {
      $this->entry= ldap_first_entry($this->_hdl, $this->_res);
      if (FALSE === $this->entry) {
        if (!($e= ldap_errno($this->_hdl))) return FALSE;
        throw new LDAPException('Could not fetch first result entry.', $e);
      }
      
      return LDAPEntry::fromResource($this->_hdl, $this->entry);
    }
    
    /**
     * Get a search entry by resource
     *
     * @param   int offset
     * @return  peer.ldap.LDAPEntry or FALSE if none exists by this offset
     * @throws  peer.ldap.LDAPException in case of a read error
     */
    public function getEntry($offset) {
      if (!$this->entries) {
        $this->entries= ldap_get_entries($this->_hdl, $this->_res);
        if (!is_array($this->entries)) {
          throw new LDAPException('Could not read result entries.', ldap_errno($this->_hdl));
        }
      }
      
      if (!isset($this->entries[$offset])) return FALSE;
      return LDAPEntry::fromData($this->entries[$offset]);
    }
    
    /**
     * Gets next entry - ideal for loops such as:
     * <code>
     *   while ($entry= $l->getNextEntry()) {
     *     // doit
     *   }
     * </code>
     *
     * @return  peer.ldap.LDAPEntry or FALSE if none exists by this offset
     * @throws  peer.ldap.LDAPException in case of a read error
     */
    public function getNextEntry() {
      if (NULL === $this->entry) return $this->getFirstEntry();
      $this->entry= ldap_next_entry($this->_hdl, $this->entry);
      if (FALSE === $this->entry) {
      
        // Check for LDAP_TIMELIMIT_EXCEEDED and LDAP_SIZELIMIT_EXCEEDED when fetching results
        if 
          (!($e= ldap_errno($this->_hdl)) ||
          0x03 === $e ||
          0x04 === $e
        ) return FALSE;
        throw new LDAPException('Could not fetch next result entry.', $e);
      }
      
      return LDAPEntry::fromResource($this->_hdl, $this->entry);
    }

    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close() {
      return ldap_free_result($this->_res);
    }
  }
?>
