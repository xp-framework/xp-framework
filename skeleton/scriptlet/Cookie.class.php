<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * HTTP Cookie
   *
   * <quote>
   * This class represents a "Cookie", as used for session management with 
   * HTTP and HTTPS protocols. Cookies are used to get user agents (web 
   * browsers etc) to hold small amounts of state associated with a user's 
   * web browsing. Common applications for cookies include storing user 
   * preferences, automating low security user signon facilities, and 
   * helping collect data used for "shopping cart" style applications. 
   * </quote>
   *
   * @see      http://jakarta.apache.org/tomcat/tomcat-5.0-doc/servletapi/javax/servlet/http/Cookie.html
   * @see      http://home.netscape.com/newsref/std/cookie_spec.html
   * @see      rfc://2109
   * @see      php://setcookie
   * @see      xp://scriptlet.HttpScriptletResponse#setCookie
   * @see      xp://scriptlet.HttpScriptletRequest#getCookie
   * @see      xp://scriptlet.HttpScriptletRequest#getCookies
   * @purpose  Cookie header
   */
  class Cookie extends Object {
    var 
      $name         = '',
      $value        = '',
      $secure       = FALSE,
      $domain       = '',
      $path         = '',
      $expires      = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name cookie name
     * @param   string value default ''
     * @param   mixed expires default 0 the UNIX timestamp on which this cookie expires de
     * @param   string path default ''
     * @param   string domain default ''
     * @param   bool secure default FALSE
     */
    function __construct($name, $value= '', $expires= 0, $path= '', $domain= '', $secure= FALSE) {
      
      $this->name= $name;
      $this->value= $value;
      $this->expires= $expires;
      $this->expires= (is('util.Date', $expires)
        ? $expires->getTime()
        : $expires
      );
      $this->path= $path;
      $this->domain= $domain;
      $this->secure= $secure;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set Value
     *
     * @access  public
     * @param   string value
     */
    function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  string
     */
    function getValue() {
      return $this->value;
    }

    /**
     * Set Secure
     *
     * @access  public
     * @param   bool secure
     */
    function setSecure($secure) {
      $this->secure= $secure;
    }

    /**
     * Get Secure. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @access  public
     * @return  bool
     */
    function getSecure() {
      return $this->secure;
    }

    /**
     * Set Domain
     *
     * @access  public
     * @param   string domain
     */
    function setDomain($domain) {
      $this->domain= $domain;
    }

    /**
     * Get Domain. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @access  public
     * @return  string
     */
    function getDomain() {
      return $this->domain;
    }

    /**
     * Set Path
     *
     * @access  public
     * @param   string path
     */
    function setPath($path) {
      $this->path= $path;
    }

    /**
     * Get Path. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @access  public
     * @return  string
     */
    function getPath() {
      return $this->path;
    }

    /**
     * Set Expires
     *
     * @access  public
     * @param   int expires
     */
    function setExpires($expires) {
      $this->expires= (is('util.Date', $expires)
        ? $expires->getTime()
        : $expires
      );
    }

    /**
     * Get Expires. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @access  public
     * @return  int
     */
    function getExpires() {
      return $this->expires;
    }

    /**
     * Get header value representation
     *
     * @access  public
     * @return  string value
     */
    function getHeaderValue() {
      return (
        $this->name.'='.
        ($this->value === '' ? 'deleted' : $this->value).
        ($this->expires !== 0 ? '; expires='.gmdate('D, d-M-Y H:i:s \G\M\T', $this->expires) : '').
        ($this->path !== '' ? '; path='.$this->path : '').
        ($this->domain !== '' ? '; domain='.$this->domain : '').
        ($this->secure ? '; secure' : '')
      );
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        "%s@{\n".
        "  [name    ] %s\n".
        "  [value   ] %s\n".
        "  [domain  ] %s\n".
        "  [path    ] %s\n".
        "  [expires ] %s\n".
        "  [secure  ] %s\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->value,
        $this->domain,
        $this->path,
        date('r', $this->expires),
        $this->secure ? 'TRUE' : 'FALSE'
      );
    }
  }
?>
