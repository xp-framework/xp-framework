<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * DSN
   *
   * DSN syntax:
   * <pre>
   *   driver://[username[:password]]@host[:port][/database][?flag=value[&flag2=value2]]
   * </pre>
   *
   * @purpose  Unified connect string
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
     * Retrieve flags
     *
     * @access  public
     * @return  int flags
     */
    function getFlags() {
      if (!isset($this->parts['query'])) return 0;
      
      $flags= 0;
      parse_str($this->parts['query'], $config);
      foreach ($config as $key => $value) {
        if ($value) {
          $flags= $flags | constant('DB_'.strtoupper($key));
        }
      }
      return $flags;
    }

    /**
     * Retrieve value of a given parameter
     *
     * @access  public
     * @param   string key
     * @return  string value
     */    
    function getValue($key) {
      if (!isset($this->parts['query'])) return FALSE;
      
      parse_str($this->parts['query'], $config);
      if (isset ($config[$key])) 
        return $config[$key];
      
      return FALSE;
    }

    /**
     * Retrieve driver
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string driver or default if none is set
     */
    function getDriver($default= NULL) {
      return isset($this->parts['scheme']) ? $this->parts['scheme'] : $default;
    }
    
    /**
     * Retrieve host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    function getHost($default= NULL) {
      return isset($this->parts['host']) ? $this->parts['host'] : $default;
    }

    /**
     * Retrieve database
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string databse or default if none is set
     */
    function getDatabase($default= NULL) {
      return isset($this->parts['path']) ? substr($this->parts['path'], 1) : $default;
    }

    /**
     * Retrieve user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    function getUser($default= NULL) {
      return isset($this->parts['user']) ? $this->parts['user'] : $default;
    }

    /**
     * Retrieve password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    function getPassword($default= NULL) {
      return isset($this->parts['pass']) ? $this->parts['pass'] : $default;
    }

  }
?>
