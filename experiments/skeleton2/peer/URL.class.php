<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Represents a Uniform Resource Locator 
   *
   * Warning:
   * This class does not validate the URL, it simply tries its best
   * in parsing it.
   *
   * Usage example:
   * <code>
   *   $u= new URL('http://user:pass@foo.bar:8081/news/1,2,6100.html?a=a#frag');
   *   echo $u->toString();
   * </code>
   *
   * @see    php://parse_url
   */
  class URL extends Object {
    protected
      $_info = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    public function __construct($str) {
      self::setURL($str);
    }

    /**
     * Create a nice string representation
     *
     * @access  public
     * @return  string
     * @see     xp://lang.Object#toString
     */
    public function toString() {
      return sprintf(
        "%s@ {\n".
        "  [scheme]      %s\n".
        "  [host]        %s\n".
        "  [port]        %d\n".
        "  [user]        %s\n".
        "  [password]    %s\n".
        "  [path]        %s\n".
        "  [query]       %s\n".
        "  [fragment]    %s\n".
        "}",
        self::getClassName(),
        self::getScheme(),
        self::getHost(),
        self::getPort(),
        self::getUser(),
        self::getPassword(),
        self::getPath(),
        self::getQuery(),
        self::getFragment()
      );
    }

    /**
     * Retrieve scheme
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string scheme or default if none is set
     */
    public function getScheme($default= NULL) {
      return isset($this->_info['scheme']) ? $this->_info['scheme'] : $default;
    }

    /**
     * Retrieve host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    public function getHost($default= NULL) {
      return isset($this->_info['host']) ? $this->_info['host'] : $default;
    }

    /**
     * Retrieve path
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string path or default if none is set
     */
    public function getPath($default= NULL) {
      return isset($this->_info['path']) ? $this->_info['path'] : $default;
    }

    /**
     * Retrieve user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    public function getUser($default= NULL) {
      return isset($this->_info['user']) ? $this->_info['user'] : $default;
    }

    /**
     * Retrieve password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    public function getPassword($default= NULL) {
      return isset($this->_info['pass']) ? $this->_info['pass'] : $default;
    }

    /**
     * Retrieve query
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string query or default if none is set
     */
    public function getQuery($default= NULL) {
      return isset($this->_info['query']) ? $this->_info['query'] : $default;
    }

    /**
     * Retrieve fragment
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string fragment or default if none is set
     */
    public function getFragment($default= NULL) {
      return isset($this->_info['fragment']) ? $this->_info['fragment'] : $default;
    }

    /**
     * Retrieve port
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  int port or default if none is set
     */
    public function getPort($default= NULL) {
      return isset($this->_info['port']) ? $this->_info['port'] : $default;
    }

    /**
     * Retrieve parameters
     *
     * @access public
     * @return array params
     */
    public function getParams() {
      return $this->_info['params'];
    }
    
    /**
     * Get full URL
     *
     * @access  public
     * @return  string
     */
    public function getURL() {
      return $this->_info['url'];
    }
    
    /**
     * Set full URL
     *
     * @access  public
     * @param   string str URL
     */
    public function setURL($str) {
      $this->_info= parse_url($str);
      if (isset($this->_info['query'])) {
        parse_str($this->_info['query'], $this->_info['params']);
      } else {
        $this->_info['params']= array();
      }
      $this->_info['url']= $str;
    }
  }
?>
