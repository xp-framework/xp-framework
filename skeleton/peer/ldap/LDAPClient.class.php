<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  /**
   * LDAP client
   * 
   * <code>
   *   $l= &new LDAPClient('ldap.hostname.tld');
   *   try(); {
   *     $l->connect();
   *     $l->bind();
   *     $results= $l->search('o=Organization,c=Country', 
   *   } if (catch('IOException', $e)) {
   *     // handle exceptions
   *   }
   *   var_dump($results);
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function search() {
      $args= func_get_args();
      array_unshift($args, $this->_hdl);
      
      if (FALSE === ($res= call_user_func_array('ldap_search', $args))) {
        return throw(new IOException('Search failed ['.$this->getLastError().']'));
      }
      
      return ldap_get_entries($this->_hdl, $res);
    }
  }
?>
