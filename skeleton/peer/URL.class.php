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
     * Retrieve parameter by a specified name
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL  
     * @return  string url-decoded parameter value or default if none is set
     */
    function getParam($name, $default= NULL) {
      return isset($this->_info['params'][$name]) ? $this->_info['params'][$name] : $default;
    }

    /**
     * Retrieve parameters
     *
     * @access  public
     * @return  array params
     */
    function getParams() {
      return $this->_info['params'];
    }
    
    /**
     * Add a parameter
     *
     * @access  public
     * @param   string key
     * @param   string value
     */
    function addParam($key, $value) {
      $this->_info['query'].= sprintf(
        '%s%s=%s',
        ('' == $this->_info['query']) ? '' : '&',
        urlencode($key),
        urlencode($value)
      );
      parse_str($this->_info['query'], $this->_info['params']); 
      unset($this->_info['url']);   // Indicate recalculation is needed
    }

    /**
     * Add parameters from an associative array. The key is taken as
     * parameter name and the value as parameter value.
     *
     * @access  public
     * @param   array hash
     */
    function addParams($hash) {
      if ('' != $this->_info['query']) $this->_info['query'].= '&';
      
      foreach (array_keys($hash) as $key) {
        $this->_info['query'].= sprintf(
          '%s=%s&',
          urlencode($key),
          urlencode($hash[$key])
        );
      }
      $this->_info['query']= substr($this->_info['query'], 0, -1);
      parse_str($this->_info['query'], $this->_info['params']); 
      unset($this->_info['url']);   // Indicate recalculation is needed
    }

    /**
     * Retrieve whether parameters exist
     *
     * @access  public
     * @return  bool
     */
    function hasParams() {
      return !empty($this->_info['params']);
    }
    
    /**
     * Get full URL
     *
     * @access  public
     * @return  string
     */
    function getURL() {
      if (!isset($this->_info['url'])) {
        $this->_info['url']= $this->_info['scheme'].'://';
        if (isset($this->_info['user'])) $this->_info['url'].= sprintf(
          '%s%s%s@',
          $this->_info['user'],
          (isset($this->_info['pass']) ? ':' : ''),
          $this->_info['pass']
        );
        $this->_info['url'].= $this->_info['host'];
        isset($this->_info['path']) && $this->_info['url'].= $this->_info['path'];
        isset($this->_info['query']) && $this->_info['url'].= '?'.$this->_info['query'];
        isset($this->_info['fragment']) && $this->_info['url'].= '#'.$this->_info['fragment'];
      }
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
      if (isset($this->_info['user'])) $this->_info['user']= rawurldecode($this->_info['user']);
      if (isset($this->_info['pass'])) $this->_info['pass']= rawurldecode($this->_info['pass']);
      if (isset($this->_info['query'])) {
        parse_str($this->_info['query'], $this->_info['params']);
      } else {
        $this->_info['params']= array();
      }
      $this->_info['url']= $str;
    }

    /**
     * Returns a hashcode for this URL
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return md5($this->_info['url']);
    }
    
    /**
     * Returns whether a given object is equal to this.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    function equals(&$cmp) {
      return is('peer.URL', $cmp) && $this->getURL() == $cmp->getURL();
    }
  }
?>
