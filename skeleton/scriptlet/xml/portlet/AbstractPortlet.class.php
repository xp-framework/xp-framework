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
      $attributes = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->setName($name);
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
     * Initialize portlet
     *
     * @access  public
     * @return  &util.Properties properties
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
