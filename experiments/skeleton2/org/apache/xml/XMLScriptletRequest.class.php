<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.HttpScriptletRequest');

  /**
   * Wraps XML request
   *
   * The request URI shall look like this without session:
   * <pre>http://foo.bar/xml/pr:de_DE/static?__page=home<pre>
   * and like this with session:
   * <pre>http://foo.bar/xml/pr:de_DE;psessionid=cb7978876218bb7/static?__page=home<pre>
   *
   * The conforming rewrite rule for apache looks like this (one line, wrapped with _
   * for readability):
   *
   * <pre>
   * RewriteRule _
   * ^/xml/([a-zA-Z]+):([a-zA-Z_]+)(;psessionid=([0-9A-Za-z]+))?/([a-zA-Z/]+)$ /xml.php _
   * [E=PRODUCT:$1,E=LANG:$2,E=SESS:$4,E=STATE:$5,PT]
   * </pre>
   * 
   * Make sure you have a directory index file or another RewriteRule to redirect
   * to http://foo.bar/xml/pr:de_DE/static?
   *
   * @see org.apache.HttpScriptletRequest
   */
  class XMLScriptletRequest extends HttpScriptletRequest {
    public
      $defaultPage  = 'home',
      $defaultFrame = 'top',
      $state        = '',
      $language     = '',
      $page         = '';

    /**
     * Gets state
     *
     * @access  public
     * @return  string state
     */
    public function getState() {
      return $this->state;
    }

    /**
     * Sets state
     *
     * @access  public
     * @param   string state
     */
    public function setState($state) {
      $this->state= $state;
    }
    
    /**
     * Gets product
     *
     * @access  public
     * @return  string product
     */
    public function getProduct() {
      return self::getEnvValue('PRODUCT');
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
     * Sets page
     *
     * @access  public
     * @param   string page
     */
    public function setPage($page) {
      $this->page= $page;
    }

    /**
     * Gets page or default page if none is specified
     *
     * @access  public
     * @return  string page
     */
    public function getPage() {
      return empty($this->page) ? $this->defaultPage : $this->page;
    }

    /**
     * Gets frame or default frame if none is specified
     *
     * @access  public
     * @return  string page
     */
    public function getFrame() {
      if (NULL === ($frame= self::getParam('__frame'))) $frame= $this->defaultFrame;
      return $frame;
    }
    
    /**
     * Get session's Id
     *
     * @access  public
     * @return  string session id
     */
    public function getSessionId() {
      return self::getEnvValue('SESS');
    }
  }
?>
