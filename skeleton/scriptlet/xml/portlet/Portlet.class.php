<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties', 'xml.Node', 'scriptlet.xml.portlet.RunData');

  /**
   * A portlet is an implementation of a small control (rendered from its 
   * XML data) that is available to a client application.
   *
   * @purpose  Interface
   */
  class Portlet extends Interface {
  
    /**
     * Get portlet's name
     *
     * @access  public
     * @return  string name
     */
    function getName() { }
    
    /**
     * Set portlet's name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) { }
  
    /**
     * Initialize portlet
     *
     * @access  public
     * @param   &util.Properties properties
     */
    function init(&$properties) { }
    
    /**
     * Set an attribut by name
     *
     * @access  public
     * @param   string name
     * @param   &mixed value
     * @param   &scriptlet.xml.portlet.RunData rundata
     */
    function setAttribute($name, &$value, &$rundata) { }

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
    function &getAttribute($name, $default, &$rundata) { }
    
    /**
     * Retrieve portlet content as Node object
     *
     * @access  public
     * @param   &scriptlet.xml.portlet.RunData rundata
     * @return  &xml.Node
     */
    function &getContent(&$rundata) { }

    /**
     * Retrieve whether this portlet provides customization mechanisms
     *
     * @access  public
     * @return  bool
     */
    function providesCustomization() { }
  
  }
?>
