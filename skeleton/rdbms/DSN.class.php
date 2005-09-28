<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('DB_STORE_RESULT',     0x0001);
  define('DB_UNBUFFERED',       0x0002);
  define('DB_AUTOCONNECT',      0x0004);
  define('DB_PERSISTENT',       0x0008);

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
      $parts    = array(),
      $prop     = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
      $this->parts['dsn']= $str;
      foreach (parse_url($str) as $key => $value) {
        $this->parts[$key]= urldecode($value);
      }

      $this->parts['flags']= 0;
      if (isset($this->parts['query'])) {
        parse_str($this->parts['query'], $config);
        foreach ($config as $key => $value) {
          if (defined('DB_'.strtoupper ($key))) {
            if ($value) $this->parts['flags']= $this->parts['flags'] | constant('DB_'.strtoupper($key));
          } else {
            $this->prop[$key]= $value;
          }
        }
      }
    }
    
    /**
     * Retrieve flags
     *
     * @access  public
     * @return  int flags
     */
    function getFlags() {
      return $this->parts['flags'];
    }
    
    /**
     * Get a property by its name
     *
     * @access  public
     * @param   string name
     * @param   string defaullt default NULL
     * @return  string property or the default value if the property does not exist
     */
    function getProperty($name, $default= NULL) {
      return isset($this->prop[$name]) ? $this->prop[$name] : $default;
    }

    /**
     * Retrieve value of a given parameter
     *
     * @access  public
     * @param   string key
     * @param   string defaullt default NULL
     * @return  string value
     */    
    function getValue($key, $default= NULL) {
      if (!isset($this->parts['query'])) return $default;
      
      parse_str($this->parts['query'], $config);
      return isset($config[$key]) ? $config[$key] : $default;
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
     * Retrieve port
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    function getPort($default= NULL) {
      return isset($this->parts['port']) ? $this->parts['port'] : $default;
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

    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s@(%s://%s%s%s/%s%s)',
        $this->getClassName(),
        $this->parts['scheme'],
        (isset($this->parts['user']) 
          ? $this->parts['user'].(isset($this->parts['pass']) ? ':'.str_repeat('*', strlen($this->parts['pass'])) : '').'@'
          : ''
        ),
        $this->parts['host'],
        (isset($this->parts['port'])
          ? ':'.$this->parts['port']
          : ''
        ),
        $this->getDatabase() ? $this->getDatabase() : '',
        $this->parts['query'] ? '?'.$this->parts['query'] : ''
      );
    }
  }
?>
