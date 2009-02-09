<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('scriptlet.HttpScriptletURL');
  
  /**
   * Represents the XML scriptlet URLs
   *
   * @see      xp://scriptlet.xml.XMLScriptlet
   * @purpose  URL representation class
   */
  class XMLScriptletURL extends HttpScriptletURL {
      
    /**
     * Extract information from URL
     *
     */
    protected function extract() {
      if (preg_match(
        '#^/xml/((([a-zA-Z]+)\.([a-zA-Z_]+))?(\.?psessionid=([0-9A-Za-z]+))?/)?([a-zA-Z0-9/]+)$#',
        $this->getPath(),
        $parts
      )) {
        !empty($parts[3]) && $this->setProduct($parts[3]);
        !empty($parts[4]) && $this->setLanguage($parts[4]);
        !empty($parts[6]) && $this->setSessionId($parts[6]);
        !empty($parts[7]) && $this->setStateName($parts[7]);
      }

      $this->getParam('__page') && $this->setPage($this->getParam('__page'));
    }
    
    /**
     * Return value for given key or in case it's not defined return
     * the default value
     *
     * @param string key The name of the key to return
     * @return string
     */
    public function getValueOrDefault($key) {
      return $this->values->get($key) !== NULL
        ? $this->values->get($key)
        : $this->values->get('Default'.$key);
    }
    
    /**
     * Set product
     *
     * @param string product The product name
     */
    public function setProduct($product) {
      $this->values->put('Product', $product);
    }
    
    /**
     * Set the default product which is used to figure out if we really
     * need to specify the product component in URL
     *
     * @param string product
     */
    public function setDefaultProduct($product) {
      $this->values->put('DefaultProduct', $product);
    }
    
    /**
     * Get product (defaults to default product)
     *
     * @return string
     */
    public function getProduct() {
      return $this->getValueOrDefault('Product');
    }
    
    /**
     * Get default product
     *
     * @return string
     */
    public function getDefaultProduct() {
      return $this->values->get('DefaultProduct');
    }
    
    /**
     * Set language
     *
     * @param string language The language
     */
    public function setLanguage($language) {
      $this->values->put('Language', $language);
    }
    
    /**
     * Set the default language which is used to figure out if we really
     * need to specify the language component in URL
     *
     * @param string language The language
     */
    public function setDefaultLanguage($language) {
      $this->values->put('DefaultLanguage', $language);
    }
    
    /**
     * Get language (defaults to default language)
     *
     * @return string
     */
    public function getLanguage() {
      return $this->getValueOrDefault('Language');
    }

    /**
     * Get default language
     *
     * @return string
     */
    public function getDefaultLanguage() {
      return $this->values->get('DefaultLanguage');
    }

    /**
     * Set state name
     *
     * @param string stateName The state name
     */
    public function setStateName($stateName) {
      $this->values->put('StateName', $stateName);
    }
    
    /**
     * Set default state name
     *
     * @param string stateName The state name
     */
    public function setDefaultStateName($stateName) {
      $this->values->put('DefaultStateName', $stateName);
    }
    
    /**
     * Get state name (defaults to default state name)
     *
     * @return string
     */
    public function getStateName() {
      return $this->getValueOrDefault('StateName');
    }
    
    /**
     * Get default state name
     *
     * @return string
     */
    public function getDefaultStateName() {
      return $this->values->get('DefaultStateName');
    }
    
    /**
     * Set state name
     *
     * @param string stateName The state name
     */
    public function setPage($page) {
      $this->values->put('Page', $page);
    }
    
    /**
     * Set default state name
     *
     * @param string stateName The state name
     */
    public function setDefaultPage($page) {
      $this->values->put('DefaultPage', $page);
    }
    
    /**
     * Get page (defaults to default page)
     *
     * @return string
     */
    public function getPage() {
      return $this->getValueOrDefault('Page');
    }
    
    /**
     * Get default page
     *
     * @return string
     */
    public function getDefaultPage() {
      return $this->values->get('DefaultPage');
    }
    
    /**
     * Returns string representation for the URL
     *
     * @return string
     */
    public function getURL() {
    
      // Determine which settings we need to pass
      $xsr= array();
      if (
        ($this->getProduct()  != $this->getDefaultProduct()) ||
        ($this->getLanguage() != $this->getDefaultLanguage())
      ) {
        $xsr[]= $this->getProduct();
        $xsr[]= $this->getLanguage();
      }
      if ($this->getSessionId()) $xsr[]= 'psessionid='.$this->getSessionId();

      return sprintf(
        '%s://%s/xml/%s%s%s%s', 
        $this->getScheme(),
        $this->getHost(),
        (sizeof($xsr) ? implode('.', $xsr).'/' : ''),
        $this->getStateName(), 
        $this->getQuery() ? '?'.$this->getQuery() : '',
        $this->getFragment() ? '#'.$this->getFragment() : ''
      );
    }
  }
?>
