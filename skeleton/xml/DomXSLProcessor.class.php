<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.TransformerException',
    'io.FileNotFoundException',
    'xml.IXSLProcessor'
  );
  
  /**
   * XSL Processor using DomXML
   * 
   * Usage example [Transform two files]
   * <code>
   *   $proc= &new DomXSLProcessor();
   *   $proc->setXSLFile('test.xsl');
   *   $proc->setXMLFile('test.xml');
   *   
   *   try(); {
   *     $proc->run();
   *   } if (catch('TransformerException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *
   *   var_dump($proc->output());
   * </code>
   *
   * @purpose  Transform XML/XSLT using PHPs domXSL functions
   * @ext      dom
   */
  class DomXSLProcessor extends Object implements IXSLProcessor {
    public 
      $processor    = NULL,
      $stylesheet   = array(),
      $document     = NULL,
      $params       = array(),
      $output       = '';

    public
      $_base        = '',
      $_errors      = array();
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->processor= NULL;
    }

    /**
     * Set base directory
     *
     * @param   string dir
     */
    public function setBase($dir) {
      $this->_base= rtrim(realpath($dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Get base
     *
     * @return  string
     */
    public function getBase() {
      return $this->_base;
    }
    
    /**
     * Set a scheme handler
     *
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    public function setSchemeHandler($defines) {
      // Not implemented in DOM
    }

    /**
     * Set XSL file
     *
     * @param   string file file name
     * @throws  io.FileNotFoundException
     */
    public function setXSLFile($file) {
      $this->stylesheet= new DOMDocument();
      $this->stylesheet->load($this->_base.$file);
    }
    
    /**
     * Set XSL buffer
     *
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf($xsl) {
      $this->stylesheet= new DOMDocument();
      $this->stylesheet->loadXML($xsl);
    }

    /**
     * Set XML file
     *
     * @param   string file file name
     */
    public function setXMLFile($file) {
      $this->document= new DOMDocument();
      $this->document->load($file);
    }
    
    /**
     * Set XML buffer
     *
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml) {
      $this->document= new DOMDocument();
      $this->document->loadXML($xml);
    }

    /**
     * Set XSL transformation parameters
     *
     * @param   array params associative array { param_name => param_value }
     */
    public function setParams($params) {
      $this->params= $params;
    }
    
    /**
     * Set XSL transformation parameter
     *
     * @param   string name
     * @param   string value
     */
    public function setParam($name, $val) {
      $this->params[$name]= $val;
    }
    
    /**
     * Retrieve XSL transformation parameter
     *
     * @param   string name
     * @return  string value
     */
    public function getParam($name) {
      return $this->params[$name];
    }    

    /**
     * Retrieve messages generate during processing.
     *
     * @return  string[]
     */
    public function getMessages() {
      return $this->_errors;
    }

    /**
     * Run the XSL transformation
     *
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run() {
      $cwd= FALSE;

      $this->processor= new XSLTProcessor();
      $this->processor->importStyleSheet($this->stylesheet);
      $this->processor->setParameter('', $this->params);
      
      // Start transformation
      $result= NULL;
      $this->output= $this->processor->transformToXML($this->document);
      
      return TRUE;
    }

    /**
     * Retrieve the transformation's result
     *
     * @return  string
     */
    public function output() {
      return $this->output;
    }
  }
?>
