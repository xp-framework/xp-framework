<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Header');

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
   * @purpose  Cookie header
   */
  class Cookie extends Header {
    var 
      $cookie       = '',
      $secure       = FALSE,
      $domain       = '',
      $path         = '',
      $expires      = 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string cookie cookie name
     * @param   string value default ''
     * @param   int expires default 0 the UNIX timestamp on which this cookie expires de
     * @param   string path default ''
     * @param   string domain default ''
     * @param   bool secure default FALSE
     */
    function __construct($cookie, $value= '', $expires= 0, $path= '', $domain= '', $secure= FALSE) {
      $this->cookie= $cookie;
      $this->expires= $expires;
      $this->path= $path;
      $this->domain= $domain;
      $this->secure= $secure;
      parent::__construct('Cookie', $value);
    }
    
    /**
     * Get header value
     *
     * @access  public
     * @return  string value
     */
    function getValue() {
      return (
        $this->cookie.'='.
        ($this->value === '' ? 'deleted' : $this->value).
        ($this->expires !== 0 ? '; expires='.date('D, d-M-Y H:i:s \G\M\T', $this->expires) : '').
        ($this->path !== '' ? '; path='.$this->path : '').
        ($this->domain !== '' ? '; domain='.$this->domain : '').
        ($this->secure ? '; secure' : '')
      );
    }
  }
?>
