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
    public
      $parts    = array(),
      $prop     = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    public function __construct($str) {
      $this->parts= parse_url($str);
      $this->parts['dsn']= $str;
      
    }
    
    /**
     * Retrieve flags
     *
     * @access  public
     * @return  int flags
     */
    public function getFlags() {
      if (!isset($this->parts['query'])) return 0;
      
      $flags= 0;
      parse_str($this->parts['query'], $config);
      foreach ($config as $key => $value) {
        if (defined('DB_'.strtoupper ($key))) {
          if ($value) $flags= $flags | constant('DB_'.strtoupper($key));
        } else {
          $this->prop[$key]= $value;
        }
      }
      return $flags;
    }
    
    /**
     * Get a property by its name
     *
     * @access  public
     * @param   string name
     * @param   string defaullt default NULL
     * @return  string property or the default value if the property does not exist
     */
    public function getProperty($name, $default= NULL) {
      return isset($this->prop[$name]) ? $this->prop[$name] : $default;
    }

    /**
     * Retrieve value of a given parameter
     *
     * @access  public
     * @param   string key
     * @return  string value
     */    
    public function getValue($key) {
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
    public function getDriver($default= NULL) {
      return isset($this->parts['scheme']) ? $this->parts['scheme'] : $default;
    }
    
    /**
     * Retrieve host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getHost($default= NULL) {
      return isset($this->parts['host']) ? $this->parts['host'] : $default;
    }

    /**
     * Retrieve port
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getPort($default= NULL) {
      return isset($this->parts['port']) ? $this->parts['port'] : $default;
    }

    /**
     * Retrieve database
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string databse or default if none is set
     */
    public function getDatabase($default= NULL) {
      return isset($this->parts['path']) ? substr($this->parts['path'], 1) : $default;
    }

    /**
     * Retrieve user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    public function getUser($default= NULL) {
      return isset($this->parts['user']) ? $this->parts['user'] : $default;
    }

    /**
     * Retrieve password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    public function getPassword($default= NULL) {
      return isset($this->parts['pass']) ? $this->parts['pass'] : $default;
    }

  }
?>
