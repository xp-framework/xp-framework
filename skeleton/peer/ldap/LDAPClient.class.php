<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  // Search scopes
  define('LDAP_SCOPE_BASE',     0x0000);
  define('LDAP_SCOPE_ONELEVEL', 0x0001);
  define('LDAP_SCOPE_SUB',      0x0002);

  uses(
    'io.IOException',
    'peer.ldap.LDAPSearchResult'
  );
  
  /**
   * LDAP client
   * 
   * Example:
   * <code>
   *   $l= &new LDAPClient('ldap.hostname.tld');
   *   try(); {
   *     $l->connect();
   *     $l->bind();
   *     $res= &$l->search('o=Organization,c=Country', 
   *   } if (catch('IOException', $e)) {
   *     // Handle exceptions
   *   }
   *
   *   // Print results
   *   while ($entry= $l->getNextEntry()) {
   *     var_export($entry);
   *   }
   * </code>
   *
   * @see php://ldap
   * @see http://developer.netscape.com/docs/manuals/directory/41/ag/
   * @see http://developer.netscape.com/docs/manuals/dirsdk/jsdk40/contents.htm
   * @see http://perl-ldap.sourceforge.net/doc/Net/LDAP/
   * @see rfc://2251
   * @see rfc://2252
   * @see rfc://2253
   * @see rfc://2254
   * @see rfc://2255
   * @see rfc://2256
   * @ext ldap
   */
  class LDAPClient extends Object {
    var 
      $host,
      $port;
      
    var
      $_hdl;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' LDAP server
     * @param   int port default 389 Port
     */
    function __construct($host= 'localhost', $port= 389) {
      $this->host= $host;
      $this->port= $port;
      parent::__construct();
    }
    
    /**
     * Gets last error message
     *
     * @access  public
     * @return  string errormessage
     */
    function getLastError() {
      return sprintf('%d: %s', $e= ldap_error($this->_hdl), ldap_err2str($e));
    }
    
    /**
     * Connect to the LDAP server
     *
     * @access  public
     * @return  resource LDAP resource handle
     */
    function connect() {
      if (FALSE === ($this->_hdl= ldap_connect($this->host, $this->port))) {
        return throw(new IOException('Cannot connect to '.$this->host.':'.$this->port));
      }
      
      return $this->_hdl;
    }
    
    /**
     * Bind
     *
     * @access  public
     * @return  bool success
     * @throws  IOException
     */
    function bind($user= NULL, $pass= NULL) {
      if (FALSE === ($res= ldap_bind($this->_hdl, $user, $pass))) {
        return throw(new IOException('Cannot bind for '.$this->user.' ['.$this->getLastError().']'));
      }
      
      return $res;
    }
    
    /**
     * Checks whether the connection is open
     *
     * @access  public
     * @return  bool true, when we're connected, false otherwise (OK, what else?:))
     */
    function isConnected() {
      return is_resource($this->_hdl);
    }
    
    /**
     * Closes the connection
     *
     * @see     php://ldap_close
     * @access  public
     * @return  bool success
     */
    function close() {
      return $this->isConnected() ? ldap_unbind($this->_hdl) : TRUE;
    }
    
    /**
     * Perform an LDAP search with scope LDAP_SCOPE_SUB
     *
     * @access  public
     * @param   string base_dn
     * @param   string filter
     * @param   array attributes default NULL
     * @param   int attrsonly default 0,
     * @param   int sizelimit default 0
     * @param   int timelimit default 0 Time limit, 0 means no limit
     * @param   int deref one of LDAP_DEREF_*
     * @return  peer.ldap.LDAPSearchResult search result object
     * @throws  IOException
     * @see     php://ldap_search
     */
    function &search() {
      $args= func_get_args();
      array_unshift($args, $this->_hdl);
      if (FALSE === ($res= call_user_func_array('ldap_search', $args))) {
        return throw(new IOException('Search failed ['.$this->getLastError().']'));
      }
      
      return new LDAPSearchResult($this->_hdl, $res);
    }
    
    /**
     * Perform an LDAP search with a scope
     *
     * @access  public
     * @param   int scope search scope, one of the LDAP_SCOPE_* constants
     * @param   string base_dn
     * @param   string filter
     * @param   array attributes default NULL
     * @param   int attrsonly default 0,
     * @param   int sizelimit default 0
     * @param   int timelimit default 0 Time limit, 0 means no limit
     * @param   int deref one of LDAP_DEREF_*
     * @return  peer.ldap.LDAPSearchResult search result object
     * @throws  IOException
     * @see     php://ldap_search
     */
    function &searchScope() {
      $args= func_get_args();
      switch ($args[0]) {
        case LDAP_SCOPE_BASE: $func= 'ldap_read'; break;
        case LDAP_SCOPE_ONELEVEL: $func= 'ldap_list'; break;
        case LDAP_SCOPE_SUB: $func= 'ldap_search'; break;
        default: return throw(new IllegalArgumentException('Scope '.$args[0].' not supported'));
      }
      $args[0]= $this->_hdl;
      if (FALSE === ($res= call_user_func_array($func, $args))) {
        return throw(new IOException('Search failed ['.$this->getLastError().']'));
      }
      
      return new LDAPSearchResult($this->_hdl, $res);
    }
    
    /**
     * Read an entry
     *
     * @access  public
     * @param   &peer.ldap.LDAPEntry entry specifying the dn
     * @param   string filter default 'objectClass=*' filter
     * @return  &peer.ldap.LDAPEntry entry
     * @throws  IllegalArgumentException
     * @throws  IOException
     */
    function &read(&$entry, $filter= 'objectClass=*') {
      if (!is_a($entry, 'LDAPEntry')) {
        return throw(new IllegalArgumentException('Given parameter is not an LDAPEntry object'));
      }
      
      $res= ldap_list($this->_hdl, $entry->getDN(), $filter);
      if (0 != ldap_error($this->_hdl)) {
        return throw(new IOException('Read "'.$entry->getDN().'" failed ['.$this->getLastError().']'));
      }
      
      // Nothing found?
      if (FALSE === $res) return NULL;
      
      $result= ldap_get_entries($this->_hdl, $res);
      return LDAPEntry::fromData($result[0]);
    }
    
    /**
     * Check if an entry exists
     *
     * @access  public
     * @param   &peer.ldap.LDAPEntry entry specifying the dn
     * @return  bool TRUE if the entry exists
     */
    function exists(&$entry, $filter= 'objectClass=*') {
      return NULL !== $this->read($entry, $filter);
    }
    
    /**
     * Encode entries (recursively, if needed)
     *
     * @access  private
     * @param   &mixed v
     * @return  string encoded entry
     */
    function _encode(&$v) {
      if (is_array($v)) {
        foreach (array_keys($v) as $i) $v[$i]= $this->_encode($v[$i]);
        return $v;
      }
      return utf8_encode($v);
    }
    
    /**
     * Add an entry
     *
     * @access  public
     * @param   &peer.ldap.LDAPEntry entry
     * @return  bool success
     * @throws  IllegalArgumentException when entry parameter is not an LDAPEntry object
     * @throws  IOException when an error occurs during adding the entry
     */
    function add(&$entry) {
      if (!is_a($entry, 'LDAPEntry')) {
        return throw(new IllegalArgumentException('Given parameter is not an LDAPEntry object'));
      } 
      
      // This actually returns NULL on failure, not FALSE, as documented
      if (NULL == ($res= ldap_add(
        $this->_hdl, 
        $entry->getDN(), 
        array_map(array(&$this, '_encode'), $entry->getAttributes())
      ))) {
        return throw(new IOException('Add for "'.$entry->getDN().'" failed ['.$this->getLastError().']'));
      }
      
      return $res;
    }

    /**
     * Modify an entry. 
     *
     * Note: Will do a complete update of all fields and can be quite slow
     * TBD(?): Be more intelligent about what to update?
     *
     * @access  public
     * @param   &peer.ldap.LDAPEntry entry
     * @return  bool success
     * @throws  IllegalArgumentException when entry parameter is not an LDAPEntry object
     * @throws  IOException when an error occurs during adding the entry
     */
    function modify(&$entry) {
      if (!is_a($entry, 'LDAPEntry')) {
        return throw(new IllegalArgumentException('Given parameter is not an LDAPEntry object'));
      } 
      
      if (FALSE == ($res= ldap_modify(
        $this->_hdl, 
        $entry->getDN(), 
        array_map(array(&$this, '_encode'), $entry->getAttributes())
      ))) {
        return throw(new IOException('Modify for "'.$entry->getDN().'" failed ['.$this->getLastError().']'));
      }
      
      return $res;
    }

    /**
     * Delete an entry
     *
     * @access  public
     * @param   &peer.ldap.LDAPEntry entry
     * @return  bool success
     * @throws  IllegalArgumentException when entry parameter is not an LDAPEntry object
     * @throws  IOException when an error occurs during adding the entry
     */
    function delete(&$entry) {
      if (!is_a($entry, 'LDAPEntry')) {
        return throw(new IllegalArgumentException('Given parameter is not an LDAPEntry object'));
      } 
      
      if (FALSE == ($res= ldap_delete(
        $this->_hdl, 
        $entry->getDN()
      ))) {
        return throw(new IOException('Delete for "'.$entry->getDN().'" failed ['.$this->getLastError().']'));
      }
      
      return $res;
    }
    
  }
?>
