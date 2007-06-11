<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Properties',
    'xml.Node',
    'scriptlet.xml.portlet.RunData'
  );

  /**
   * A portlet is an implementation of a small control (rendered from its 
   * XML data) that is available to a client application.
   *
   * @purpose  Interface
   */
  interface Portlet {
  
    /**
     * Get portlet's name
     *
     * @return  string name
     */
    public function getName();
    
    /**
     * Set portlet's name
     *
     * @param   string name
     */
    public function setName($name);
  
    /**
     * Initialize portlet
     *
     * @param   util.Properties properties
     */
    public function init($properties);
    
    /**
     * Set an attribut by name
     *
     * @param   string name
     * @param   mixed value
     * @param   scriptlet.xml.portlet.RunData rundata
     */
    public function setAttribute($name, $value, $rundata);

    /**
     * Get an attribute by name. Returns default value if the specified 
     * value is non-existant.
     *
     * @param   string name
     * @param   mixed default
     * @param   scriptlet.xml.portlet.RunData rundata
     * @return  mixed
     */
    public function getAttribute($name, $default, $rundata);
    
    /**
     * Retrieve portlet content as Node object
     *
     * @param   scriptlet.xml.portlet.RunData rundata
     * @return  xml.Node
     */
    public function getContent($rundata);

    /**
     * Retrieve whether this portlet provides customization mechanisms
     *
     * @return  bool
     */
    public function providesCustomization();
  
  }
?>
