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
     * @param   var callback
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
     * Set XSL from a tree
     *
     * @param   xml.Tree xsl
     */
    public function setXSLTree(Tree $xsl);
    
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
     * Set XML from a tree
     *
     * @param   xml.Tree xml
     */
    public function setXMLTree(Tree $xml);

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

    /**
     * Retrieve the transformation's result's encoding
     *
     * @return  string
     */
    public function outputEncoding();
  }
?>
