<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

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
   * @see php-doc://ldap
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
     * Closes the connection
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return ldap_close($this->_hdl);
    }
    
    /**
     * Perform an LDAP search
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
     * @see     php-doc://ldap_search
     */
    function &search() {
      $args= func_get_args();
      array_unshift($args, $this->_hdl);
      
      $this->results= NULL;
      if (FALSE === ($res= call_user_func_array('ldap_search', $args))) {
        return throw(new IOException('Search failed ['.$this->getLastError().']'));
      }
      
      return new LDAPSearchResult($this->_hdl, $res);
    }
    
    /**
     * Encode entries (recursively, if needed)
     *
     * @access  private
     * @param   &mixed v
     * @return  string encoded entry
     */
    function _encode(&$v) {
      if (is_array($v)) for ($i= 0, $m= sizeof($v); $i < $m; $i++) {
        $v[$i]= $this->_encode($v[$i]);
        return $v;
      }
      return utf8_encode($v);
    }
    
    /**
     * Add an entry
     *
     * @access  public
     * @param   peer.ldap.LDAPEntry entry
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
        return throw(new IOException('Add failed ['.$this->getLastError().']'));
      }
      
      return $res;
    }
    
  }
?>
