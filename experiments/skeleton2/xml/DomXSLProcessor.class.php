<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.XML',
    'xml.TransformerException'
  );

  /**
   * XSL Processor using DomXML
   * 
   * Usage example [Transform two files]
   * <code>
   *   $proc= new DomXSLProcessor();
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
   * @ext      dom
   * @see      http://www.gingerall.com - Sablotron
   */
  class DomXSLProcessor extends XML {
    public
      $stylesheet   = array(),
      $document     = NULL,
      $params       = array(),
      $output       = '';
      
    protected
      $_base        = '';

    /**
     * Constructor
     *
     * @access  public
     * @params  array params default NULL
     */
    public function __construct($params= NULL) {
      parent::__construct($params);
      $this->processor= NULL;
    }
    
    /**
     * Set an error handler
     *
     * @access  public
     * @param   mixed callback
     */
    public function setErrorHandler($funcName) {
      // Not implemented in DOM
    }

    /**
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    public function setSchemeHandler($defines) {
      // Not implemented in DOM
    }

    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    public function setBase($dir) {
      $this->_base= rtrim($dir, '/').'/';
    }

    /**
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     */
    public function setXSLFile($file) {
      $this->stylesheet= array(0, $file);
    }
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf($xsl) {
      $this->stylesheet= array(1, &$xsl);
    }

    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     */
    public function setXMLFile($file) {
      $this->document= domxml_open_file($file);
    }
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml) {
      $this->document= domxml_open_mem($xml);
    }

    /**
     * Set XSL transformation parameters
     *
     * @access  public
     * @param   array params associative array { param_name => param_value }
     */
    public function setParams($params) {
      $this->params= $params;
    }
    
    /**
     * Set XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    public function setParam($name, $val) {
      $this->params[$name]= $val;
    }

    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run() {
    
      // Hack for missing DomXSL->setBase() functionality - preserve
      // original directory, change to base directory and change it back
      // after transformation
      $cwd= FALSE;
      if (!empty($this->_base)) {
        $cwd= getcwd();
        chdir($this->_base);
      }
      
      // Get stylesheet
      $proc= FALSE;
      switch ($this->stylesheet[0]) {
        case 0: $proc= domxml_xslt_stylesheet_file($this->stylesheet[1]); break;
        case 1: $proc= domxml_xslt_stylesheet($this->stylesheet[1]);
      }
      
      // Transform this
      $result= FALSE;
      $proc && $result= $proc->process($this->document, $this->params, FALSE);
      $cwd && chdir($cwd);
      
      if (!$result) {
        throw (new TransformerException('Transformation failed'));
      }
      
      // Copy output from transformation
      $this->output= $proc->result_dump_mem($result);
      return TRUE;
    }

    /**
     * Retrieve the transformation's result
     *
     * @access  public
     * @return  string
     */
    public function output() {
      return $this->output;
    }

    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      if ($this->document) { with ($n= $this->document->document_element); {
        $n && $n->unlink_node($n);
      }}
      $this->document= NULL;
      parent::__destruct();
    }
  }
?>
