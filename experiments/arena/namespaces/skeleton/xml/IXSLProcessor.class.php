<?php
/* This class is part of the XP framework
 *
 * $Id: IXSLProcessor.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace xml;

  /**
   * Interface IXSLProcessor
   *
   * @purpose  Interface for XSL processor classes
   */
  interface IXSLProcessor {
  
    /**
     * Retrieve messages generate during processing.
     *
     * @return  string[]
     */
    public function getMessages();
    
    /**
     * Set a scheme handler
     *
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    public function setSchemeHandler($cb);
    
    /**
     * Set base directory
     *
     * @param   string dir
     */
    public function setBase($dir);
    
    /**
     * Get base
     *
     * @return  string
     */
    public function getBase();
    
    /**
     * Set XSL file
     *
     * @param   string file file name
     */
    public function setXSLFile($file);
    
    /**
     * Set XSL buffer
     *
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf($xsl);
    
    /**
     * Set XML file
     *
     * @param   string file file name
     */
    public function setXMLFile($file);    
    
    /**
     * Set XML buffer
     *
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml);

    /**
     * Set XSL transformation parameters
     *
     * @param   array params associative array { param_name => param_value }
     */
    public function setParams($params);
    
    /**
     * Set XSL transformation parameter
     *
     * @param   string name
     * @param   string value
     */
    public function setParam($name, $value);
    
    /**
     * Retrieve XSL transformation parameter
     *
     * @param   string name
     * @return  string value
     */
    public function getParam($name);
    
    /**
     * Run the XSL transformation
     *
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run();
    
    /**
     * Retrieve the transformation's result
     *
     * @return  string
     */
    public function output();
  }
?>
