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
   * @test   xp://net.xp_framework.unittest.peer.URLTest
   * @see    php://parse_url
   */
  class URL extends Object {
    public $_info= array();
      
    /**
     * Constructor
     *
     * @param   string str
     * @throws  lang.FormatException if string is unparseable
     */
    public function __construct($str= NULL) {
      if (NULL !== $str) $this->setURL($str);
    }

    /**
     * Creates a string representation of this URL
     *
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
     * @param   var default default NULL  
     * @return  string scheme or default if none is set
     */
    public function getScheme($default= NULL) {
      return isset($this->_info['scheme']) ? $this->_info['scheme'] : $default;
    }
    
    /**
     * Set scheme
     *
     * @param   string scheme
     * @return  peer.URL this object
     */
    public function setScheme($scheme) {
      $this->_info['scheme']= $scheme;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve host
     *
     * @param   var default default NULL  
     * @return  string host or default if none is set
     */
    public function getHost($default= NULL) {
      return isset($this->_info['host']) ? $this->_info['host'] : $default;
    }
    
    /**
     * Set host
     *
     * @param   string host 
     * @return  peer.URL this object
     */
    public function setHost($host) {
      $this->_info['host']= $host;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve path
     *
     * @param   var default default NULL  
     * @return  string path or default if none is set
     */
    public function getPath($default= NULL) {
      return isset($this->_info['path']) ? $this->_info['path'] : $default;
    }
    
    /**
     * Set path
     *
     * @param   string path 
     * @return  peer.URL this object
     */
    public function setPath($path) {
      $this->_info['path']= $path;
      unset($this->_info['url']);
      return $this;
    }    

    /**
     * Retrieve user
     *
     * @param   var default default NULL  
     * @return  string user or default if none is set
     */
    public function getUser($default= NULL) {
      return isset($this->_info['user']) ? $this->_info['user'] : $default;
    }
    
    /**
     * Set user
     *
     * @param   string user 
     * @return  peer.URL this object
     */
    public function setUser($user) {
      $this->_info['user']= $user;
      unset($this->_info['url']);
      return $this;
    }    

    /**
     * Retrieve password
     *
     * @param   var default default NULL  
     * @return  string password or default if none is set
     */
    public function getPassword($default= NULL) {
      return isset($this->_info['pass']) ? $this->_info['pass'] : $default;
    }

    /**
     * Set password
     *
     * @param   string password 
     * @return  peer.URL this object
     */
    public function setPassword($password) {
      $this->_info['pass']= $password;
      unset($this->_info['url']);
      return $this;
    }    
    
    /**
     * Calculates query string
     *
     * @param   string key
     * @param   var value
     * @param   string prefix The postfix to use for each variable (defaults to '')
     * @return  string
     */
    protected function buildQuery($key, $value, $postfix= '') {
      $query= '';
      if (is_array($value)) {
        if (is_int(key($value))) {
          foreach ($value as $i => $v) {
            $query.= $this->buildQuery(NULL, $v, $postfix.$key.'[]');
          }
        } else {
          foreach ($value as $k => $v) {
            $query.= $this->buildQuery(NULL, $v, $postfix.$key.'['.$k.']');
          }
        }
      } else if ('' === $value) {
        $query.= '&'.urlencode($key).$postfix;
      } else {
        $query.= '&'.urlencode($key).$postfix.'='.urlencode($value);
      }
      return $query;
    }

    /**
     * Parses a query string. Replaces builtin string parsing as that 
     * breaks (by design) on query parameters with dots inside, e.g.
     *
     * @see     php://parse_str
     * @param   string query
     * @return  [:var] parsed parameters
     */
    protected function parseQuery($query) {
      $params= array();
      foreach (explode('&', $query) as $pair) {
        sscanf($pair, "%[^=]=%[^\r]", $key, $value);
        $key= urldecode($key);
        if (substr_count($key, '[') !== substr_count($key, ']')) {
          throw new FormatException('Unbalanced [] in query string');
        }
        if ($start= strpos($key, '[')) {    // Array notation
          $base= substr($key, 0, $start);
          isset($params[$base]) || $params[$base]= array();
          $ptr= &$params[$base];
          $offset= 0;
          do {
            $end= strpos($key, ']', $offset);
            if ($start === $end- 1) {
              $ptr= &$ptr[];
            } else {
              $end+= substr_count($key, '[', $start+ 1, $end- $start- 1);
              $ptr= &$ptr[substr($key, $start+ 1, $end- $start- 1)];
            }
            $offset= $end+ 1;
          } while ($start= strpos($key, '[', $offset));
          $ptr= urldecode($value);
        } else {
          $params[$key]= urldecode($value);
        }
      }
      return $params;
    }

    /**
     * Retrieve query
     *
     * @param   var default default NULL  
     * @return  string query or default if none is set
     */
    public function getQuery($default= NULL) {
      if (!$this->_info['params']) return $default;
      $query= '';
      foreach ($this->_info['params'] as $key => $value) {
        $query.= $this->buildQuery($key, $value);
      }
      return substr($query, 1);
    }

    /**
     * Set query
     *
     * @param   string query 
     * @return  peer.URL this object
     */
    public function setQuery($query) {
      $this->_info['params']= $this->parseQuery($query);
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve fragment
     *
     * @param   var default default NULL  
     * @return  string fragment or default if none is set
     */
    public function getFragment($default= NULL) {
      return isset($this->_info['fragment']) ? $this->_info['fragment'] : $default;
    }

    /**
     * Set fragment
     *
     * @param   string fragment 
     * @return  peer.URL this object
     */
    public function setFragment($fragment) {
      $this->_info['fragment']= $fragment;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve port
     *
     * @param   var default default NULL  
     * @return  int port or default if none is set
     */
    public function getPort($default= NULL) {
      return isset($this->_info['port']) ? $this->_info['port'] : $default;
    }
    
    /**
     * Set port
     *
     * @param   int port 
     * @return  peer.URL this object
     */
    public function setPort($port) {
      $this->_info['port']= $port;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve parameter by a specified name
     *
     * @param   string name
     * @param   var default default NULL  
     * @return  string url-decoded parameter value or default if none is set
     */
    public function getParam($name, $default= NULL) {
      return isset($this->_info['params'][$name]) ? $this->_info['params'][$name] : $default;
    }

    /**
     * Retrieve parameters
     *
     * @return  array params
     */
    public function getParams() {
      return $this->_info['params'];
    }
    
    /**
     * Set a parameter
     *
     * @param   string key
     * @param   var value either a string or a string[]
     * @return  peer.URL this object
     */
    public function setParam($key, $value= '') {
      $this->_info['params'][$key]= $value;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Set parameters
     *
     * @param   array<string, var> hash parameters
     * @return  peer.URL this object
     */
    public function setParams($hash) {
      foreach ($hash as $key => $value) {
        $this->setParam($key, $value);
      }
      unset($this->_info['url']);
      return $this;
    }
    
    /**
     * Add a parameter
     *
     * @param   string key
     * @param   var value either a string or a string[]
     * @return  peer.URL this object
     */
    public function addParam($key, $value= '') {
      if (isset($this->_info['params'][$key])) {
        throw new IllegalArgumentException('A parameter named "'.$key.'" already exists');
      }
      $this->_info['params'][$key]= $value;
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Add parameters from an associative array. The key is taken as
     * parameter name and the value as parameter value.
     *
     * @param   array<string, var> hash parameters
     * @return  peer.URL this object
     */
    public function addParams($hash) {
      $params= $this->_info['params'];
      try {
        foreach ($hash as $key => $value) {
          $this->addParam($key, $value);
        }
      } catch (IllegalArgumentException $e) {
        $this->_info['params']= $params;
        throw $e;
      }
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Remove a parameter
     *
     * @param   string key
     * @return  peer.URL this object
     */
    public function removeParam($key) {
      unset($this->_info['params'][$key]);
      unset($this->_info['url']);
      return $this;
    }

    /**
     * Retrieve whether a parameter with a given name exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasParam($name) {
      return isset($this->_info['params'][$name]);
    }

    /**
     * Retrieve whether parameters exist
     *
     * @return  bool
     */
    public function hasParams() {
      return !empty($this->_info['params']);
    }
    
    /**
     * Get full URL
     *
     * @return  string
     */
    public function getURL() {
      if (!isset($this->_info['url'])) {
        $this->_info['url']= $this->_info['scheme'].'://';
        if (isset($this->_info['user'])) $this->_info['url'].= sprintf(
          '%s%s@',
          $this->_info['user'],
          (isset($this->_info['pass']) ? ':'.$this->_info['pass'] : '')
        );
        $this->_info['url'].= $this->_info['host'];
        isset($this->_info['port']) && $this->_info['url'].= ':'.$this->_info['port'];
        isset($this->_info['path']) && $this->_info['url'].= $this->_info['path'];
        if ($this->_info['params']) {
          $this->_info['url'].= '?'.$this->getQuery();
        }
        isset($this->_info['fragment']) && $this->_info['url'].= '#'.$this->_info['fragment'];
      }
      return $this->_info['url'];
    }
    
    /**
     * Set full URL
     *
     * @param   string str URL
     * @throws  lang.FormatException if string is unparseable
     */
    public function setURL($str) {
      if (!strstr($str, '://') || !($this->_info= parse_url($str))) {
        $e= new FormatException('Cannot parse "'.$str.'"');
        xp::gc(__FILE__);
        throw $e;
      }
      
      if (isset($this->_info['user'])) $this->_info['user']= rawurldecode($this->_info['user']);
      if (isset($this->_info['pass'])) $this->_info['pass']= rawurldecode($this->_info['pass']);
      if (isset($this->_info['query'])) {
        $this->_info['params']= $this->parseQuery($this->_info['query']);
        unset($this->_info['query']);
      } else {
        $this->_info['params']= array();
      }
      $this->_info['url']= $str;
    }

    /**
     * Returns a hashcode for this URL
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->getURL());
    }
    
    /**
     * Returns whether a given object is equal to this.
     *
     * @param   lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->getURL() === $cmp->getURL();
    }
  }
?>
