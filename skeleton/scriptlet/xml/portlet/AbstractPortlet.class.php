<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract portlet
   *
   * @see      xp://scriptlet.xml.portlet.Portlet
   * @purpose  Abstract base class
   */
  class AbstractPortlet extends Object {
    var
      $name       = '',
      $properties = NULL,
      $layout     = NULL,
      $attributes = array();

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->setName(substr(get_class($this), 0, -1* strlen('Portlet')));
    }

    /**
     * Get portlet's name
     *
     * @access  public
     * @return  string name
     */
    function getName() { 
      return $this->name;
    }
    
    /**
     * Set portlet's name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) { 
      $this->name= $name;
    }
    
    /**
     * Set layout of portlet usage
     *
     * @access  public
     * @param   string layout
     */
    function setLayout($layout) {
      $this->layout= $layout;
    }
    
    /**
     * Get layout of portlet usage
     *
     * @access  public
     * @return  string layout
     */
    function getLayout() {
      return $this->layout;
    }
  
    /**
     * Initialize portlet
     *
     * @access  public
     */
    function init(&$properties) { 
      $this->properties= &$properties;
    }
    
    /**
     * Set an attribut by name
     *
     * @access  public
     * @param   string name
     * @param   &mixed value
     * @param   &scriptlet.xml.portlet.RunData rundata
     */
    function setAttribute($name, &$value, &$rundata) {
      $this->attributes[$name]= &$value;
    }

    /**
     * Get an attribute by name. Returns default value if the specified 
     * value is non-existant.
     *
     * @access  public
     * @param   string name
     * @param   mixed default
     * @param   &scriptlet.xml.portlet.RunData rundata
     * @return  &mixed
     */
    function &getAttribute($name, $default, &$rundata) {
      if (!array_key_exists($name, $this->attributes)) return $default;

      return $this->attributes[$name];
    }
    
    /**
     * Retrieve portlet content as Node object
     *
     * @model   abstract
     * @access  public
     * @param   &scriptlet.xml.portlet.RunData rundata
     * @return  &xml.Node
     */
    function &getContent(&$rundata) { }

    /**
     * Retrieve whether this portlet provides customization mechanisms.
     * Returns FALSE in this default implementation
     *
     * @access  public
     * @return  bool
     */
    function providesCustomization() { 
      return FALSE;
    }
  
  } implements(__FILE__, 'scriptlet.xml.portlet.Portlet');
?>
