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
    protected static
      $defaultPorts= array(
        'http' => 80,
        'https'=> 443
      );

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
      if ('' === $query) return array();

      $params= array();
      foreach (explode('&', $query) as $pair) {
        $key= $value= NULL;
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
      $this->_info['params']= $this->parseQuery((string)$query);
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
          rawurlencode($this->_info['user']),
          (isset($this->_info['pass']) ? ':'.rawurlencode($this->_info['pass']) : '')
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
      if (!preg_match('!^([a-z][a-z0-9\+]*)://([^@/?#]+@)?([^/?#]*)(/([^#?]*))?(.*)$!', $str, $matches)) {
        throw new FormatException('Cannot parse "'.$str.'"');
      }
      
      $this->_info= array();
      $this->_info['scheme']= $matches[1];

      // Credentials
      if ('' !== $matches[2]) {
        sscanf($matches[2], '%[^:@]:%[^@]@', $user, $password);
        $this->_info['user']= rawurldecode($user);
        $this->_info['pass']= NULL === $password ? NULL : rawurldecode($password);
      } else {
        $this->_info['user']= NULL;
        $this->_info['pass']= NULL;
      }

      // Host and port, optionally
      if ('' === $matches[3] && '' !== $matches[4]) {
        $this->_info['host']= NULL;
      } else {
        if (!preg_match('!^([a-zA-Z0-9\.-]+|\[[^\]]+\])(:([0-9]+))?$!', $matches[3], $host)) {
          throw new FormatException('Cannot parse "'.$str.'": Host and/or port malformed');
        }
        $this->_info['host']= $host[1];
        $this->_info['port']= isset($host[2]) ? (int)$host[3] : NULL;
      }
      
      // Path
      if ('' === $matches[4]) {
        $this->_info['path']= NULL;
      } else if (strlen($matches[4]) > 3 && (':' === $matches[4]{2} || '|' === $matches[4]{2})) {
        $this->_info['path']= $matches[4]{1}.':'.substr($matches[4], 3);
      } else {
        $this->_info['path']= $matches[4];
      }

      // Query string and fragment
      if ('' === $matches[6] || '?' === $matches[6] || '#' === $matches[6]) {
        $this->_info['params']= array();
        $this->_info['fragment']= NULL;
      } else if ('#' === $matches[6]{0}) {
        $this->_info['params']= array();
        $this->_info['fragment']= substr($matches[6], 1);
      } else if ('?' === $matches[6]{0}) {
        $p= strcspn($matches[6], '#');
        $this->_info['params']= $this->parseQuery(substr($matches[6], 1, $p- 1));
        $this->_info['fragment']= $p >= strlen($matches[6])- 1 ? NULL : substr($matches[6], $p+ 1);
      }
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
    
    /**
     * Capitalize letters in escape sequence
     *
     * @param  string string
     * @return  string
     */
    protected function capitalizeLettersInEscapeSequence($string) {
      return preg_replace_callback('/%[\w]{2}/',
        create_function('$matches','return strtoupper($matches[0]);'),
        $string
      );
    }
    
    /**
     * Decode percent encoded octets
     * 
     * @see http://www.apps.ietf.org/rfc/rfc3986.html#sec-2.3
     * @param  string string
     * @return  string
     */
    protected function decodePercentEncodedOctets($string) {
      $unreserved = array();
        
      for($octet= 65; $octet <= 90; $octet++) {
        $unreserved[]= dechex($octet);
      }

      for($octet= 97; $octet<=122; $octet++) {
        $unreserved[]= dechex($octet);
      }

      for($octet= 48; $octet<=57; $octet++) {
        $unreserved[]= dechex($octet);
      }

      $unreserved[]= dechex(ord( '-' ));
      $unreserved[]= dechex(ord( '.' ));
      $unreserved[]= dechex(ord( '_' ));
      $unreserved[]= dechex(ord( '~' ));

      return preg_replace_callback( 
        array_map(create_function('$str', 'return "/%".strtoupper($str)."/x";'), $unreserved), 
        create_function('$matches', 'return chr(hexdec($matches[0]));'),
        $string
      );
    }
    
    /**
     * Remove dot segments
     *
     * @see http://www.apps.ietf.org/rfc/rfc3986.html#sec-5.2.4
     * @param  string string
     * @return  string
     */
    protected function removeDotSegments($path){
      $cleanPath = '';

      // A. If the input begins with a prefix of "../" or "./"
      $patterns['stepA']   = '!^(\.\./|\./)!';
      // B1. If the input begins with a prefix of "/./"
      $patterns['stepB1'] = '!^(/\./)!';
      // B2. If the input begins with a prefix of "/."
      $patterns['stepB2'] = '!^(/\.)$!';
      // C. If the input begins with a prefix of "/../" or "/.."
      $patterns['stepC']   = '!^(/\.\./|/\.\.)!';
      // D. If the input consists only of "." or ".."
      $patterns['stepD']   = '!^(\.|\.\.)$!';
      // E. Move the first path segment in the input to the end of the output
      $patterns['stepE']   = '!(/*[^/]*)!';

      while(!empty($path)) {
        switch (TRUE) {
          case preg_match($patterns['stepA'], $path):
            $path= preg_replace($patterns['stepA'], '', $path);
          break;

          case preg_match($patterns['stepB1'], $path, $matches):
          case preg_match($patterns['stepB2'], $path, $matches):
            $path= preg_replace('!^'.$matches[1].'!', '/', $path);
          break;

          case preg_match($patterns['stepC'], $path, $matches):
            $path= preg_replace('!^'.preg_quote($matches[1], '!').'!', '/', $path);
            $cleanPath= preg_replace('!/([^/]+)$!', '', $cleanPath);
          break;

          case preg_match($patterns['stepD'], $path):
            $path= preg_replace($patterns['stepD'], '', $path);
          break;

          case preg_match($patterns['stepE'], $path, $matches):
            $path= preg_replace('/^'.preg_quote($matches[1], '/').'/', '', $path, 1);
            $cleanPath.= $matches[1];
          break;
        }
      }
      return $cleanPath;
    }
    
    /**
     * Check if current port is the default one for this scheme
     *
     * @see http://www.apps.ietf.org/rfc/rfc3986.html#sec-5.2.4
     * @param  string scheme
     * @param  string port
     * @return  bool
     */
    protected function isDefaultPort($scheme, $port) {
      return (array_key_exists($scheme, self::$defaultPorts) && $port==self::$defaultPorts[$scheme]);
    }
    
    /**
     * Get standard URL 
     *
     * @see http://tools.ietf.org/html/rfc3986#page-38
     * @return  string
     */
    public function getCanonicalURL() {
      sscanf($this->_info['scheme'], '%[^+]', $scheme);
      
      // Convert the scheme to lower case
      $url= strtolower($scheme).'://';

      // Convert the host to lower case
      $url.= strtolower($this->_info['host']);
      
      // Add port if exist and is not the default one for this scheme
      if (isset($this->_info['port']) && !$this->isDefaultPort($scheme, $this->_info['port']))
        $url.= ':'.$this->_info['port'];
      
      // Adding trailing /
      $url.= '/';
      
      // Capitalize letters in escape sequences &
      // Decode percent-encoded octets of unreserved characters &
      // Remove dot-segments
      if (isset($this->_info['path'])) {
        $path= $this->capitalizeLettersInEscapeSequence($this->_info['path']);
        $path= $this->decodePercentEncodedOctets($path);
        $path= $this->removeDotSegments($path);
        $url.= (strstr($path, '/')!==0) ? substr($path, 1) : $path;
      }
      
      // Same steps as for path
      if ($this->_info['params']) {
        $query= $this->capitalizeLettersInEscapeSequence($this->getQuery());
        $query= $this->decodePercentEncodedOctets($query);
        $url.= '?'.$this->removeDotSegments($query);
      }
      
      // Same steps as for path
      if (isset($this->_info['fragment'])) {
        $fragment= $this->capitalizeLettersInEscapeSequence($this->_info['fragment']);
        $fragment= $this->decodePercentEncodedOctets($fragment);
        $url.= '#'.$this->removeDotSegments($fragment);
      }
      
      return $url;
    }
  }
?>
