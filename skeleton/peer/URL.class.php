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
   *   $u= &new URL('http://user:pass@foo.bar:8081/news/1,2,6100.html?a=a#frag');
   *   echo $u->toString();
   * </code>
   *
   * @see    php://parse_url
   */
  class URL extends Object {
    var
      $_info = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
      $this->setURL($str);
    }

    /**
     * Create a nice string representation
     *
     * @access  public
     * @return  string
     * @see     xp://lang.Object#toString
     */
    function toString() {
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
        $this->getClassName(),
        $this->getScheme(),
        $this->getHost(),
        $this->getPort(),
        $this->getUser(),
        $this->getPassword(),
        $this->getPath(),
        $this->getQuery(),
        $this->getFragment()
      );
    }

    /**
     * Retrieve scheme
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string scheme or default if none is set
     */
    function getScheme($default= NULL) {
      return isset($this->_info['scheme']) ? $this->_info['scheme'] : $default;
    }

    /**
     * Retrieve host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    function getHost($default= NULL) {
      return isset($this->_info['host']) ? $this->_info['host'] : $default;
    }

    /**
     * Retrieve path
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string path or default if none is set
     */
    function getPath($default= NULL) {
      return isset($this->_info['path']) ? $this->_info['path'] : $default;
    }

    /**
     * Retrieve user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    function getUser($default= NULL) {
      return isset($this->_info['user']) ? $this->_info['user'] : $default;
    }

    /**
     * Retrieve password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    function getPassword($default= NULL) {
      return isset($this->_info['pass']) ? $this->_info['pass'] : $default;
    }

    /**
     * Retrieve query
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string query or default if none is set
     */
    function getQuery($default= NULL) {
      return isset($this->_info['query']) ? $this->_info['query'] : $default;
    }

    /**
     * Retrieve fragment
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string fragment or default if none is set
     */
    function getFragment($default= NULL) {
      return isset($this->_info['fragment']) ? $this->_info['fragment'] : $default;
    }

    /**
     * Retrieve port
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  int port or default if none is set
     */
    function getPort($default= NULL) {
      return isset($this->_info['port']) ? $this->_info['port'] : $default;
    }

    /**
     * Retrieve parameters
     *
     * @access public
     * @return array params
     */
    function getParams() {
      return $this->_info['params'];
    }
    
    /**
     * Get full URL
     *
     * @access  public
     * @return  string
     */
    function getURL() {
      return $this->_info['url'];
    }
    
    /**
     * Set full URL
     *
     * @access  public
     * @param   string str URL
     */
    function setURL($str) {
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
