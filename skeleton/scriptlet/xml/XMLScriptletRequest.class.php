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
   * RewriteRule _
   * ^/xml/([a-zA-Z]+)\.([a-zA-Z_]+)(\.psessionid=([0-9A-Za-z]+))?/([a-zA-Z/]+)$ /xml.php _
   * [E=PRODUCT:$1,E=LANGUAGE:$2,E=SESS:$4,E=STATE:$5,PT]
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
      $page         = '';

    /**
     * Initialize this request object
     *
     * @access  public
     */
    public function initialize() {
      parent::initialize();
      $this->product= $this->getEnvValue('PRODUCT');
      $this->stateName= $this->getEnvValue('STATE');
      $this->language= $this->getEnvValue('LANGUAGE');
      $this->page= isset($_REQUEST['__page']) ? $_REQUEST['__page'] : 'home';
    }

    /**
     * Set Page
     *
     * @access  public
     * @param   string page
     */
    public function setPage($page) {
      $this->page= $page;
    }

    /**
     * Get Page
     *
     * @access  public
     * @return  string
     */
    public function getPage() {
      return $this->page;
    }

    /**
     * Gets state
     *
     * @access  public
     * @return  string stateName
     */
    public function getStateName() {
      return $this->stateName;
    }

    /**
     * Sets state
     *
     * @access  public
     * @param   string stateName
     */
    public function setStateName($stateName) {
      $this->stateName= $stateName;
    }
    
    /**
     * Gets product
     *
     * @access  public
     * @return  string product
     */
    public function getProduct() {
      return $this->product;
    }

    /**
     * Sets product
     *
     * @access  public
     * @param   string product
     */
    public function setProduct($product) {
      $this->product= $product;
    }

    /**
     * Gets language
     *
     * @access  public
     * @return  string language
     */
    public function getLanguage() {
      return $this->language;
    }

    /**
     * Sets Language
     *
     * @access  public
     * @param   string language
     */
    public function setLanguage($language) {
      $this->language= $language;
    }
    
    /**
     * Get session's Id
     *
     * @access  public
     * @return  string session id
     */
    public function getSessionId() {
      return $this->getEnvValue('SESS');
    }
  }
?>
