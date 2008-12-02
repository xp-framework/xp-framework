<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('scriptlet.HttpScriptletRequest');
  
  /**
   * Wraps XML request
   *
   * The request URI shall look like this without session:
   * <pre>http://foo.bar/xml/pr.de_DE/static?__page=home<pre>
   * and like this with session:
   * <pre>http://foo.bar/xml/pr.de_DE.psessionid=cb7978876218bb7/static?__page=home<pre>
   *
   * The conforming rewrite rule for apache looks like this (one line, wrapped with _
   * for readability):
   *
   * <pre>
   * RewriteRule ^/xml /index.php [PT]
   * </pre>
   * 
   * Make sure you have a directory index file or another RewriteRule to redirect
   * to http://foo.bar/xml/pr.de_DE/static?
   *
   * @see      xp://scriptlet.HttpScriptletRequest
   * @purpose  Scriptlet request wrapper
   */
  class XMLScriptletRequest extends HttpScriptletRequest {
    public
      $product      = '',
      $stateName    = '',
      $language     = '',
      $page         = '',
      $sessionId    = '';

    /**
     * Sets request's URI
     *
     * @param   peer.URL uri a uri representated by peer.URL
     */
    public function setURI($uri) {
      with ($this->uri= $uri); {
        $this->uri->setDefaultProduct($this->getDefaultProduct());
        $this->uri->setDefaultLanguage($this->getDefaultLanguage());
        $this->uri->setDefaultStateName($this->getDefaultStateName());
        $this->uri->setDefaultPage($this->getDefaultPage());
        
        // Check cookies for session id
        $this->setSessionId($this->hasCookie('session_id')
          ? $this->getCookie('session_id')->getValue()
          : $this->uri->getSessionId()
        );
        
        $this->setProduct($this->uri->getProduct());
        $this->setLanguage($this->uri->getLanguage());
        $this->setStateName($this->uri->getStateName());
        $this->setPage($this->uri->getPage());
      }
    }
    
    /**
     * Set Page
     *
     * @param   string page
     */
    public function setPage($page) {
      $this->page= $page;
    }

    /**
     * Get Page
     *
     * @return  string
     */
    public function getPage() {
      return $this->page;
    }

    /**
     * Gets default page (defaults to DEF_PAGE environment variable, if not
     * set default to "home")
     *
     * @return  string page
     */
    public function getDefaultPage() {
      return $this->getEnvValue('DEF_PAGE', 'home');
    }

    /**
     * Gets state
     *
     * @return  string stateName
     */
    public function getStateName() {
      return $this->stateName;
    }

    /**
     * Gets default state (defaults to DEF_STATE environment variable, if not
     * set default to "static")
     *
     * @return  string stateName
     */
    public function getDefaultStateName() {
      return $this->getEnvValue('DEF_STATE', 'static');
    }

    /**
     * Sets state
     *
     * @param   string stateName
     */
    public function setStateName($stateName) {
      $this->stateName= $stateName;
    }
    
    /**
     * Gets product
     *
     * @return  string product
     */
    public function getProduct() {
      return $this->product;
    }

    /**
     * Gets default product
     *
     * @return  string product
     */
    public function getDefaultProduct() {
      return $this->getEnvValue('DEF_PROD');
    }

    /**
     * Sets product
     *
     * @param   string product
     */
    public function setProduct($product) {
      $this->product= $product;
    }

    /**
     * Gets language
     *
     * @return  string language
     */
    public function getLanguage() {
      return $this->language;
    }

    /**
     * Gets default language (defaults to DEF_LANG environment variable, if not
     * set default to "en_US")
     *
     * @return  string language
     */
    public function getDefaultLanguage() {
      return $this->getEnvValue('DEF_LANG', 'en_US');
    }

    /**
     * Sets Language
     *
     * @param   string language
     */
    public function setLanguage($language) {
      $this->language= $language;
    }
    
    /**
     * Sets session id
     *
     * @param   string session
     */
    public function setSessionId($session) {
      $this->sessionId= $session;
    }

    /**
     * Get session's Id. This overwrites the parent's implementation 
     * of fetching the id from the request parameters. XMLScriptlets 
     * need to have the session id passed through the request URL or
     * cookie.
     *
     * @return  string session id
     */
    public function getSessionId() {
      return $this->sessionId;
    }
  }
?>
