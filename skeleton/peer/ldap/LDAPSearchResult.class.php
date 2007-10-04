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
      $size= NULL;

    protected
      $_hdl= NULL,
      $_res= NULL,
      $_id= NULL;

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
     * @return  mixed entry or FALSE if there is no such entry
     */
    public function getFirstEntry() {
      return $this->getEntry(ldap_first_entry($this->_hdl, $this->_res));
    }
    
    /**
     * Get a search entry by resource
     *
     * @param   int id
     * @return  mixed entry or FALSE if none exists by this offset
     * @throws  lang.IllegalStateException in case no search has been performed before
     */
    public function getEntry($id) {
      if (NULL == $this->size) {
        throw(new IllegalStateException('Please perform a search first'));
      }

      $this->_id= $id;
      if (FALSE === $id) return FALSE;
      return LDAPEntry::fromData($this->_hdl, $this->_id);
    }
    
    /**
     * Gets next entry - ideal for loops such as:
     * <code>
     *   while ($entry= $l->getNextEntry()) {
     *     // doit
     *   }
     * </code>
     *
     * @return  mixed entry or FALSE if there are none more
     */
    public function getNextEntry() {
      if (NULL === $this->_id) return $this->getFirstEntry();
      return $this->getEntry(ldap_next_entry($this->_hdl, $this->_id));
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
