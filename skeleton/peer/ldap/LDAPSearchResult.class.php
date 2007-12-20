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
      $this->entry= array(ldap_first_entry($this->_hdl, $this->_res));
      if (FALSE === $this->entry[0]) {
        if (!($e= ldap_errno($this->_hdl))) return FALSE;
        throw new LDAPException('Could not fetch first result entry.', $e);
      }
      
      $this->entry[1]= 1;
      return LDAPEntry::fromResource($this->_hdl, $this->entry[0]);
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
    
      // Check if we were called without getFirstEntry() being called first
      // Tolerate this situation by simply returning whatever getFirstEntry()
      // returns.
      if (NULL === $this->entry) {
        return $this->getFirstEntry();
      }
      
      // If we have reached the number of results reported by ldap_count_entries()
      // - see constructor, return FALSE without trying to read further. Trying
      // to read "past the end" results in LDAP error #84 (decoding error) in some 
      // client/server constellations, which is then incorrectly reported as an error.
      if ($this->entry[1] >= $this->size) {
        return FALSE;
      }
      
      // Fetch the next entry. Return FALSE if it was the last one (where really,
      // we shouldn't be getting here)
      $this->entry[0]= ldap_next_entry($this->_hdl, $this->entry[0]);
      if (FALSE === $this->entry[0]) {
        if (!($e= ldap_errno($this->_hdl))) return FALSE;
        throw new LDAPException('Could not fetch next result entry.', $e);
      }
      
      // Keep track how many etnries we have fetched so we stop once we
      // have reached this number - see above for explanation.
      $this->entry[1]++;
      return LDAPEntry::fromResource($this->_hdl, $this->entry[0]);
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
