<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XML', 'xml.TransformerException');
  
  /**
   * XSL Processor
   * 
   * Usage example [Transform two files]
   * <code>
   *   $proc= &new XSLProcessor();
   *   $proc->setXSLFile('test.xml');
   *   $proc->setXMLFile('test.xsl');
   *   
   *   try(); {
   *     $proc->run();
   *   } if (catch('TransformerException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   * </code>
   *
   * @purpose  Transform XML/XSLT using PHPs builtin XSL functions
   * @ext      xslt
   * @see      http://www.gingerall.com - Sablotron
   */
  class XSLProcessor extends XML {
    var 
      $processor    = NULL,
      $stylesheet   = array(),
      $buffer       = array(),
      $params       = array(),
      $output       = '';

    var
      $_base        = '';

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->processor= xslt_create();
    }
    
    /**
     * Set an error handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_error_handler
     */
    function setErrorHandler($funcName) {
      xslt_set_error_handler($this->processor, $funcName);
    }

    /**
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    function setSchemeHandler($defines) {
      xslt_set_scheme_handlers($this->processor, $defines);
    }

    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    function setBase($dir) {
      $this->_base= rtrim($dir, '/').'/';
    }

    /**
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     */
    function setXSLFile($file) {
      $this->stylesheet= array($this->_base.$file, NULL);
    }
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    function setXSLBuf($xsl) {
      $this->stylesheet= array('arg:/_xsl', $xsl);
    }

    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     */
    function setXMLFile($file) {
      $this->buffer= array($this->_base.$file, NULL);
    }
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    function setXMLBuf($xml) {
      $this->buffer= array('arg:/_xml', $xml);
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
    function run($buffers= array()) {
      if (NULL != $this->buffer[1]) $buffers['/_xml']= &$this->buffer[1];
      if (NULL != $this->stylesheet[1]) $buffers['/_xsl']= &$this->stylesheet[1];
      
      if (FALSE === ($this->output= xslt_process(
        $this->processor, 
        $this->buffer[0],
        $this->stylesheet[0],
        NULL,
        $buffers,
        $this->params
      ))) {
        return throw(new TransformerException('Transformation failed'));
      }
      
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
      if ($this->processor) {
        xslt_free($this->processor);
        $this->processor= NULL;
      }
      parent::__destruct();
    }
  }
?>
