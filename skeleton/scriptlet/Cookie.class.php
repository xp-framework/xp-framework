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
   * @see      http://www.owasp.org/index.php/HTTPOnly 
   * @see      rfc://2109
   * @see      php://setcookie
   * @see      xp://scriptlet.HttpScriptletResponse#setCookie
   * @see      xp://scriptlet.HttpScriptletRequest#getCookie
   * @see      xp://scriptlet.HttpScriptletRequest#getCookies
   * @test     xp://net.xp_framework.unittest.scriptlet.CookieTest
   * @purpose  Cookie header
   */
  class Cookie extends Object {
    public 
      $name         = '',
      $value        = '',
      $secure       = FALSE,
      $httpOnly     = FALSE,
      $domain       = '',
      $path         = '',
      $expires      = 0;

    /**
     * Constructor
     *
     * @param   string name cookie name
     * @param   string value default ''
     * @param   var expires default 0 the UNIX timestamp on which this cookie expires de
     * @param   string path default ''
     * @param   string domain default ''
     * @param   bool secure default FALSE
     * @param   bool httpOnly default FALSE
     */
    public function __construct(
      $name, 
      $value= '', 
      $expires= 0, 
      $path= '', 
      $domain= '', 
      $secure= FALSE,
      $httpOnly= FALSE
    ) {
      $this->name= $name;
      $this->value= $value;
      $this->expires= $expires;
      $this->expires= is('util.Date', $expires) ? $expires->getTime() : $expires;
      $this->path= $path;
      $this->domain= $domain;
      $this->secure= $secure;
      $this->httpOnly= $httpOnly;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Value
     *
     * @param   string value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @return  string
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Set Secure. Indicates that the cookie should only be transmitted 
     * over a secure HTTPS connection from the client. 
     *
     * @param   bool secure
     */
    public function setSecure($secure) {
      $this->secure= $secure;
    }

    /**
     * Get Secure. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @return  bool
     */
    public function getSecure() {
      return $this->secure;
    }

    /**
     * Set HttpOnly. When TRUE the cookie will be made accessible only 
     * through the HTTP protocol, not by JavaScript.
     *
     * @param   bool httpOnly
     */
    public function setHttpOnly($httpOnly) {
      $this->httpOnly= $httpOnly;
    }

    /**
     * Get HttpOnly. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @return  bool
     */
    public function getHttpOnly() {
      return $this->HttpOnly;
    }

    /**
     * Set Domain
     *
     * @param   string domain
     */
    public function setDomain($domain) {
      $this->domain= $domain;
    }

    /**
     * Get Domain. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @return  string
     */
    public function getDomain() {
      return $this->domain;
    }

    /**
     * Set Path
     *
     * @param   string path
     */
    public function setPath($path) {
      $this->path= $path;
    }

    /**
     * Get Path. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @return  string
     */
    public function getPath() {
      return $this->path;
    }

    /**
     * Set Expires
     *
     * @param   int expires
     */
    public function setExpires($expires) {
      $this->expires= is('util.Date', $expires) ? $expires->getTime() : $expires;
    }

    /**
     * Get Expires. Note: This value is not available for cookies retrieved
     * from HttpScriptletRequest, as the browser does not submit this in
     * subsequent requests.
     *
     * @return  int
     */
    public function getExpires() {
      return $this->expires;
    }

    /**
     * Get header value representation
     *
     * @return  string value
     */
    public function getHeaderValue() {
      return (
        $this->name.'='.
        ($this->value === '' ? 'deleted' : $this->value).
        ($this->expires !== 0 ? '; expires='.gmdate('D, d-M-Y H:i:s \G\M\T', $this->expires) : '').
        ($this->path !== '' ? '; path='.$this->path : '').
        ($this->domain !== '' ? '; domain='.$this->domain : '').
        ($this->secure ? '; secure' : '').
        ($this->httpOnly ? '; HTTPOnly' : '')
      );
    }
    
    /**
     * Create string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s<%s=%s>@{\n".
        "  [domain  ] %s\n".
        "  [path    ] %s\n".
        "  [expires ] %s\n".
        "  [secure  ] %s\n".
        "  [HTTPOnly] %s\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->value,
        $this->domain,
        $this->path,
        date('r', $this->expires),
        $this->secure ? 'TRUE' : 'FALSE',
        $this->httpOnly ? 'TRUE' : 'FALSE'
      );
    }
  }
?>
