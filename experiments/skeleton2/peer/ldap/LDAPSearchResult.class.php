<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ldap.LDAPEntry');

  /**
   * Wraps ldap search results
   *
   * @see php://ldap_get_entries
   */
  class LDAPSearchResult extends Object {
    public
      $data= NULL,
      $size= 0;
      
    protected
      $_offset= 0;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   array result returnvalue of ldap_get_entries()
     */
    public function __construct($hdl, $res) {
      $this->data= ldap_get_entries($hdl, $res);
      $this->size= $this->data['count'];
      
    }
    
    /**
     * Returns number of found elements
     *
     * @access  public
     * @return  int
     */
    public function numEntries() {
      return $this->size;
    }
    
    /**
     * Gets first entry
     *
     * @access  public
     * @return  mixed entry or FALSE if there is no such entry
     */
    public function getFirstEntry() {
      return self::getEntry($this->_offset= 0);
    }
    
    /**
     * Get a search entry by offset
     *
     * @access  public
     * @param   int offset
     * @return  mixed entry or FALSE if none exists by this offset
     * @throws  IllegalStateException in case no search has been performed before
     */
    public function getEntry($offset) {     
      if (NULL == $this->data) {
        throw (new IllegalStateException('Please perform a search first'));
      }
     
      return (isset($this->data[$offset])
        ? LDAPEntry::fromData($this->data[$offset])
        : FALSE
      );
    }
    
    /**
     * Gets next entry - ideal for loops such as:
     * <code>
     *   while ($entry= $l->getNextEntry()) {
     *     // doit
     *   }
     * </code>
     *
     * @access  public
     * @return  mixed entry or FALSE if there are none more
     */
    public function getNextEntry() {
      return self::getEntry($this->_offset++);
    }

  }
?>
