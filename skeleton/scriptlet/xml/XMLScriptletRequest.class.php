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
     * Initialize this request object
     *
     */
    public function initialize() {
      parent::initialize();
      
      // Use default first
      $this->product=   $this->getEnvValue('PRODUCT', $this->getDefaultProduct());
      $this->stateName= $this->getEnvValue('STATE', $this->getDefaultStateName());
      $this->language=  $this->getEnvValue('LANGUAGE', $this->getDefaultLanguage());
      $this->sessionId= $this->getEnvValue('SESS');
      
      // Check cookies for session id
      if ($this->hasCookie('session_id')) {
        $this->sessionId= $this->getCookie('session_id')->getValue();
      }

      // Parse path to determine current state, language and product - if not parseable,
      // just fall back to the defaults
      if (preg_match(
        '#^/xml/((([a-zA-Z]+)\.([a-zA-Z_]+))?(\.?psessionid=([0-9A-Za-z]+))?/)?([a-zA-Z/]+)$#',
        $this->getURL()->getPath(),
        $part
      )) {
        !empty($part[3]) && $this->setProduct($part[3]);
        !empty($part[4]) && $this->setLanguage($part[4]);
        !empty($part[6]) && $this->sessionId= $part[6];
        !empty($part[7]) && $this->setStateName($part[7]);
      }
      
      $this->page= isset($_REQUEST['__page']) ? $_REQUEST['__page'] : 'home';
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
     * Gets state
     *
     * @return  string stateName
     */
    public function getStateName() {
      return $this->stateName;
    }

    /**
     * Gets default state
     *
     * @return  string stateName
     */
    public function getDefaultStateName() {
      return $this->getEnvValue('DEF_STATE');
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
     * Gets default language
     *
     * @return  string language
     */
    public function getDefaultLanguage() {
      return $this->getEnvValue('DEF_LANG');
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
