<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * LDAP client
   * 
   * Example:
   * <code>
   *   $l= &new LDAPClient('ldap.hostname.tld');
   *   try(); {
   *     $l->connect();
   *     $l->bind();
   *     $l->search('o=Organization,c=Country', 
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
   * @ext ldap
   */
  class LDAPClient extends Object {
    var 
      $host,
      $port;
      
    var 
      $user,
      $pass;
      
    var
      $_hdl;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' LDAP server
     * @param   int port default 389 Port
     */
    function __construct($host= 'localhost', $port= 389, $user= NULL, $pass= NULL) {
      $this->host= $host;
      $this->port= $port;
      $this->user= $user;
      $this->pass= $pass;
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
    function bind() {
      if (FALSE === ($res= ldap_bind($this->_hdl, $this->user, $this->pass))) {
        return throw(new IOException('Cannot bind for '.$this->user.' ['.$this->getLastError().']'));
      }
      
      return $res;
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
     * @return  int number of found objects
     * @throws  IOException
     * @see     php-doc://ldap-search
     */
    function search() {
      $args= func_get_args();
      array_unshift($args, $this->_hdl);
      
      $this->results= NULL;
      if (FALSE === ($res= call_user_func_array('ldap_search', $args))) {
        return throw(new IOException('Search failed ['.$this->getLastError().']'));
      }
      
      $this->results= ldap_get_entries($this->_hdl, $res);
      return $this->results['count'];
    }
    
    /**
     * Get a search entry by offset
     *
     * @access  public
     * @param   int offset
     * @return  mixed entry or FALSE if none exists by this offset
     */
    function getEntry($offset) {     
      if (NULL == $this->results) {
        return throw(new IllegalArgumentException('Please perform a search first'));
      }
     
      return isset($this->results[$offset]) ? $this->results[$offset] : FALSE;
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
      static $offset= 0;
      
      return $this->getEntry($offset++);
    }
  }
?>
