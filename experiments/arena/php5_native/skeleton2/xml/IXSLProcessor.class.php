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
     * @access  public
     * @return  string[]
     */
    public function getMessages();
    
    /**
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    public function setSchemeHandler($cb);
    
    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    public function setBase($dir);
    
    /**
     * Get base
     *
     * @access  public
     * @return  string
     */
    public function getBase();
    
    /**
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     */
    public function setXSLFile($file);
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf();
    
    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     */
    public function setXMLFile($file);    
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml);

    /**
     * Set XSL transformation parameters
     *
     * @access  public
     * @param   array params associative array { param_name => param_value }
     */
    public function setParams();
    
    /**
     * Set XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    public function setParam();
    
    /**
     * Retrieve XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    public function getParam($name);
    
    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run();
    
    /**
     * Retrieve the transformation's result
     *
     * @access  public
     * @return  string
     */
    public function output();                                
  }
?>
