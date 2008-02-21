<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.URL');

  define('DB_STORE_RESULT',     0x0001);
  define('DB_UNBUFFERED',       0x0002);
  define('DB_AUTOCONNECT',      0x0004);
  define('DB_PERSISTENT',       0x0008);
  define('DB_NEWLINK',          0x0010);

  /**
   * DSN
   *
   * DSN syntax:
   * <pre>
   *   driver://[username[:password]]@host[:port][/database][?flag=value[&flag2=value2]]
   * </pre>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.DSNTest
   * @purpose  Unified connect string
   */
  class DSN extends Object {
    public 
      $url      = NULL,
      $dsn      = array(),
      $flags    = 0,
      $prop     = array();
      
    /**
     * Constructor
     *
     * @param   string str
     */
    public function __construct($str) {
      $this->url= new URL($str);
      $this->dsn= $str;

      if ($config= $this->url->getParams()) {
        foreach ($config as $key => $value) {
          if (defined('DB_'.strtoupper($key))) {
            if ($value) $this->flags= $this->flags | constant('DB_'.strtoupper($key));
          } else {
            $this->prop[$key]= $value;
          }
        }
      }
    }
    
    /**
     * Retrieve flags
     *
     * @return  int flags
     */
    public function getFlags() {
      return $this->flags;
    }
    
    /**
     * Get a property by its name
     *
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
     * @param   string key
     * @param   string defaullt default NULL
     * @return  string value
     */
    #[@deprecated('Duplicates getProperty()')]
    public function getValue($key, $default= NULL) {
      if (!isset($this->parts['query'])) return $default;
      
      parse_str($this->parts['query'], $config);
      return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * Retrieve driver
     *
     * @param   mixed default default NULL  
     * @return  string driver or default if none is set
     */
    public function getDriver($default= NULL) {
      return $this->url->getScheme() ? $this->url->getScheme() : $default;
    }
    
    /**
     * Retrieve host
     *
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getHost($default= NULL) {
      return $this->url->getHost() ? $this->url->getHost() : $default;
    }

    /**
     * Retrieve port
     *
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getPort($default= NULL) {
      return $this->url->getPort() ? $this->url->getPort() : $default;
    }

    /**
     * Retrieve database
     *
     * @param   mixed default default NULL  
     * @return  string databse or default if none is set
     */
    public function getDatabase($default= NULL) {
      return $this->url->getPath() ? substr($this->url->getPath(), 1) : $default;
    }

    /**
     * Retrieve user
     *
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    public function getUser($default= NULL) {
      return $this->url->getUser() ? $this->url->getUser() : $default;
    }

    /**
     * Retrieve password
     *
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    public function getPassword($default= NULL) {
      return $this->url->getPassword() ? $this->url->getPassword() : $default;
    }

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s@(%s://%s%s%s/%s%s)',
        $this->getClassName(),
        $this->url->getScheme(),
        ($this->url->getUser() 
          ? $this->url->getUser().($this->url->getPassword() ? ':'.str_repeat('*', strlen($this->url->getPassword())) : '').'@'
          : ''
        ),
        $this->url->getHost(),
        ($this->url->getPort()
          ? ':'.$this->url->getPort()
          : ''
        ),
        $this->getDatabase() ? $this->getDatabase() : '',
        $this->url->getQuery() ? '?'.$this->url->getQuery() : ''
      );
    }
  }
?>
