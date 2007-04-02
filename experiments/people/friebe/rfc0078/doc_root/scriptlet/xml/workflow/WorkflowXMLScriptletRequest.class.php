<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('scriptlet.HttpScriptletRequest');
  
  /**
   * Wraps XML request
   *
   * The conforming rewrite rule for apache looks like this:
   * <pre>
   *  RewriteRule ^/xml/* /index.php
   * </pre>
   *
   * @see      xp://scriptlet.HttpScriptletRequest
   * @purpose  Scriptlet request wrapper
   */
  class WorkflowXMLScriptletRequest extends HttpScriptletRequest {
    public
      $product      = '',
      $stateName    = '',
      $language     = '';

    /**
     * Initialize this request object
     *
     */
    public function initialize() {
      parent::initialize();

      // Parse URL format
      sscanf($this->getUrl()->getPath(), '/xml/%[^/]/%s', $opt, $this->stateName);
      sscanf($opt, '%[^.].%[^.].psessionid=%s', $this->product, $this->language, $this->params['psessionid']);
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
     * Sets Language
     *
     * @param   string language
     */
    public function setLanguage($language) {
      $this->language= $language;
    }
  }
?>
