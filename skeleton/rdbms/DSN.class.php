<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * DSN
   *
   * DSN examples:
   * <pre>
   *   type://username:password@host:port/database/table
   * </pre>
   */
  class DSN extends Object {
    var 
      $parts    = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
      $this->parts= parse_url($str);
      $this->parts['dsn']= $str;
      parent::__construct();
    }
    
    /**
     * Retreive host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    function getHost($default= NULL) {
      return isset($this->parts['host']) ? $this->parts['host'] : $default;
    }

    /**
     * Retreive user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    function getUser($default= NULL) {
      return isset($this->pass['user']) ? $this->pass['user'] : $default;
    }

    /**
     * Retreive password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    function getPassword($default= NULL) {
      return isset($this->pass['password']) ? $this->pass['password'] : $default;
    }

  }
?>
