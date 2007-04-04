<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'scriptlet.HttpScriptletRequest',
    'lang.reflect.Package'
  );
  
  /**
   * Wraps XML request
   *
   * The conforming rewrite rule for apache looks like this:
   * <pre>
   *  RewriteRule ^/!(image)/* /index.php
   * </pre>
   *
   * @see      xp://scriptlet.HttpScriptletRequest
   * @purpose  Scriptlet request wrapper
   */
  class WorkflowXMLScriptletRequest extends HttpScriptletRequest {
    public
      $area         = '',
      $product      = '',
      $stateName    = '',
      $language     = '';

    protected
      $package = NULL;

    /**
     * Constructor
     *
     * @param   string classpath
     */
    function __construct($classpath) {
      $this->package= Package::forName($classpath);
    }

    /**
     * Initialize this request object
     *
     */
    public function initialize() {
      parent::initialize();

      // Parse URL format
      sscanf($this->getUrl()->getPath(), '/%[^/]/%s', $opt, $rest);
      sscanf($opt, '%[^.].%[^.].%[^.].psessionid=%s', $areaname, $this->product, $this->language, $this->params['psessionid']);

      // Try to find area:
      //
      // 1) User package for {NAME}Area 
      // 2) Default implementation scriptlet.xml.workflow.areas.Default{NAME}Area
      $area= ($areaname ? ucfirst($areaname) : 'Public').'Area';
      if ($this->package->providesClass($area)) {
        $class= $this->package->loadClass($area)->newInstance();
      } else {
        $class= XPClass::forName(sprintf('scriptlet.xml.workflow.areas.Default%s', $area));
      }
      $this->area= $class->newInstance();
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
