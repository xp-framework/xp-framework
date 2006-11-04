<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XMLFormatException', 'xml.TransformerException', 'io.FileNotFoundException');
  
  /**
   * XSL Processor
   * 
   * @purpose  Transform XML/XSLT using MSXML
   * @ext      com
   * @see      http://msdn.microsoft.com/xml/ - MS XML developer center
   */
  class MsXslProcessor extends Object {
    var
      $template = NULL,
      $input    = NULL;

    var
      $_base= '';
      
    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    function setBase($dir) {
      $this->_base= rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Get base
     *
     * @access  public
     * @return  string
     */
    function getBase() {
      return $this->_base;
    }

    /**
     * Retrieve messages generate during processing.
     *
     * @access  public
     * @return  string[]
     */
    function getMessages() {
      return array();   // TBI
    }

    /**
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    function setSchemeHandler($defines) {
      // TBI
    }

    /**
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     * @throws  io.FileNotFoundException
     */
    function setXSLFile($file) {
      if (!file_exists($this->_base.$file)) {
        return throw(new FileNotFoundException($this->_base.$file.' not found'));
      }

      $dom= &new COM('MSXML2.FreeThreadedDOMDocument');
      $dom->async= FALSE;
      if ($dom->load($this->_base.$file)) {
        $this->template= &new COM('MSXML2.XSLTemplate');
        $this->template->stylesheet= $dom->documentElement;
      }
      $dom->release();
      $dom= NULL;
    }
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    function setXSLBuf($xsl) {
      $dom= &new COM('MSXML2.FreeThreadedDOMDocument');
      $dom->async= FALSE;
      if ($dom->loadXML($xsl)) {
        $this->template= &new COM('MSXML2.XSLTemplate');
        $this->template->stylesheet= $dom->documentElement;
      }
      $dom->release();
      $dom= NULL;
    }

    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     * @throws  io.FileNotFoundException
     */
    function setXMLFile($file) {
      if (!file_exists($file)) {
        return throw(new FileNotFoundException($file.' not found'));
      }

      $this->input= &new COM('MSXML2.DOMDocument');
      $this->input->async= FALSE;
      if (!$this->input->load($file)) {
        $this->input->release();
        $this->input= NULL;
      }
    }
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    function setXMLBuf($xml) {
      $this->input= &new COM('MSXML2.DOMDocument');
      $this->input->async= FALSE;
      if (!$this->input->loadXML($xml)) {
        $this->input->release();
        $this->input= NULL;
      }
    }

    /**
     * Set XSL transformation parameters
     *
     * @access  public
     * @param   array params associative array { param_name => param_value }
     */
    function setParams($params) {
      $this->params= $params;
    }
    
    /**
     * Retrieve XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    function getParam($name) {
      return $this->params[$name];
    }    

    /**
     * Set XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setParam($name, $val) {
      $this->params[$name]= $val;
    }

    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    function run() {
      if (!$this->template) return throw(new TransformerException('Template malformed'));
      if (!$this->input) return throw(new TransformerException('Input malformed'));

      if (!($proc= &$this->template->createProcessor())) {
        return throw(new TransformerException('Syntax error'));
      }
      
      // Pass parameters, define input...
      foreach ($this->params as $name => $value) {
        $proc->addParameter($name, $value);
      }
      $proc->input= $this->input;

      // ...and transform
      if (TRUE !== $proc->transform()) {
        $proc->release();
        $proc= NULL;
        return throw(new TransformerException('Transformation failed'));
      }
      
      $this->output= $proc->output;
      $proc->release();
      $proc= NULL;
      return TRUE;
    }

    /**
     * Retrieve the transformation's result
     *
     * @access  public
     * @return  string
     */
    function output() {
      return $this->output;
    }

    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->template && $this->template->release();
      $this->template= NULL;
      $this->input && $this->input->release();
      $this->input= NULL;
    }

  } implements(__FILE__, 'xml.IXSLProcessor');
?>
