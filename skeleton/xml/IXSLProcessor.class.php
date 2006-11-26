<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface IXSLProcessor
   *
   * @purpose  Interface for XSL processor classes
   */
  class IXSLProcessor extends Interface {
  
    /**
     * Retrieve messages generate during processing.
     *
     * @access  public
     * @return  string[]
     */
    function getMessages() { }
    
    /**
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    function setSchemeHandler($cb) { }
    
    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    function setBase($dir) { }
    
    /**
     * Get base
     *
     * @access  public
     * @return  string
     */
    function getBase() { }
    
    /**
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     */
    function setXSLFile($file) { }
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    function setXSLBuf($xsl) { }
    
    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     */
    function setXMLFile($file) { }    
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    function setXMLBuf($xml) { }

    /**
     * Set XSL transformation parameters
     *
     * @access  public
     * @param   array params associative array { param_name => param_value }
     */
    function setParams($params) { }
    
    /**
     * Set XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setParam($name, $value) { }
    
    /**
     * Retrieve XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    function getParam($name) { }
    
    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    function run() { }
    
    /**
     * Retrieve the transformation's result
     *
     * @access  public
     * @return  string
     */
    function output() { }
  }
?>
