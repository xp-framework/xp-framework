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
    var
      $defaultPage  = 'home',
      $defaultFrame = 'top',
      $state        = '';

    /**
     * Gets state
     *
     * @access  public
     * @return  string state
     */
    function getState() {
      return $this->state;
    }

    /**
     * Sets state
     *
     * @access  public
     * @param   string state
     */
    function setState($state) {
      $this->state= $state;
    }
    
    /**
     * Gets product
     *
     * @access  public
     * @return  string product
     */
    function getProduct() {
      return $this->getEnvValue('PRODUCT');
    }

    /**
     * Gets language
     *
     * @access  public
     * @return  string language
     */
    function getLanguage() {
      return $this->getEnvValue('LANG');
    }
    
    /**
     * Sets page
     *
     * @access  public
     * @param   string page
     */
    function setPage($page) {
      $this->params['__page']= $page;
    }

    /**
     * Gets page or default page if none is specified
     *
     * @access  public
     * @return  string page
     */
    function getPage() {
      if (NULL === ($page= $this->getParam('__page'))) $page= $this->defaultPage;
      return $page;
    }

    /**
     * Gets frame or default frame if none is specified
     *
     * @access  public
     * @return  string page
     */
    function getFrame() {
      if (NULL === ($frame= $this->getParam('__frame'))) $frame= $this->defaultFrame;
      return $frame;
    }
    
    /**
     * Get session's Id
     *
     * @access  public
     * @return  string session id
     */
    function getSessionId() {
      return $this->getEnvValue('SESS');
    }
  }
?>
