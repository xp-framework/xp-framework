<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('peer.ldap.LDAPEntry');

  /**
   * Wraps ldap search results
   *
   * @see php-doc://ldap_get_entries
   */
  class LDAPSearchResult extends Object {
    var
      $data= NULL,
      $size= 0;
      
    var
      $_offset= 0;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   array result returnvalue of ldap_get_entries()
     */
    function __construct(&$hdl, $res) {
      $this->data= ldap_get_entries($hdl, $res);
      $this->size= $this->data['count'];
      parent::__construct();
    }
    
    /**
     * Gets first entry
     *
     * @access  public
     * @return  mixed entry or FALSE if there is no such entry
     */
    function getFirstEntry() {
      return $this->getEntry($this->_offset= 0);
    }
    
    /**
     * Get a search entry by offset
     *
     * @access  public
     * @param   int offset
     * @return  mixed entry or FALSE if none exists by this offset
     * @throws  IllegalStateException in case no search has been performed before
     */
    function getEntry($offset) {     
      if (NULL == $this->data) {
        return throw(new IllegalStateException('Please perform a search first'));
      }
     
      // No more entries
      if (!isset($this->data[$offset])) return FALSE;
      
      $e= &new LDAPEntry($this->data[$offset]['dn']);
      foreach (array_keys($this->data[$offset]) as $key) {
        if ('count' == $key || is_int($key)) continue;
        
        $e->attributes[$key]= $this->data[$offset][$key];
        unset($e->attributes[$key]['count']);
      }      
      
      return $e;
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
    function getNextEntry() {
      return $this->getEntry($this->_offset++);
    }

  }
?>
