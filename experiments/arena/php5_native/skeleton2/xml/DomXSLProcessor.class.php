<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.XML',
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
  class DomXSLProcessor extends XML implements IXSLProcessor {
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
     * @access  public
     */
    public function __construct() {
      $this->processor= NULL;
    }

    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    public function setBase($dir) {
      $this->_base= rtrim(realpath($dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Get base
     *
     * @access  public
     * @return  string
     */
    public function getBase() {
      return $this->_base;
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
     * Set XSL file
     *
     * @access  public
     * @param   string file file name
     * @throws  io.FileNotFoundException
     */
    public function setXSLFile($file) {
      if (!file_exists($this->_base.$file)) {
        throw(new FileNotFoundException($this->_base.$file.' not found'));
      }
      $this->stylesheet= array(0, $this->_base.$file);
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
      $this->document= domxml_open_file($this->_base.$file);
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
     * Retrieve XSL transformation parameter
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    public function getParam($name) {
      return $this->params[$name];
    }    

    /**
     * Error handler callback
     *
     * @access  private
     * @param   int code
     * @param   string msg
     * @param   string file
     * @param   int line
     * @see     php://set_error_handler
     */
    public function _traperror($code, $msg, $file, $line) {
      @list($method, $message)= explode(':', trim($msg), 2);
      if (in_array($method, array(
        'domxml_xslt_stylesheet()', 
        'domxml_xslt_stylesheet_file()',
        'process()',
      ))) {
        $message && $this->_errors[]= trim($message);
        return;
      }

      __error($code, $msg, $file, $line);
    }
    
    /**
     * Retrieve messages generate during processing.
     *
     * @access  public
     * @return  string[]
     */
    public function getMessages() {
      return $this->_errors;
    }

    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run() {
      $cwd= FALSE;

      $this->_errors= array();
      set_error_handler(array(&$this, '_traperror'));

      // Get stylesheet
      switch ($this->stylesheet[0]) {
        case 0: 
          $proc= domxml_xslt_stylesheet_file($this->stylesheet[1]); 
          break;

        case 1:
          if ($this->_base) {
            $cwd= getcwd();
            chdir($this->_base);
          }
          $proc= domxml_xslt_stylesheet($this->stylesheet[1]);
          break;

        default:
          $proc= FALSE;
      }
      
      // Start transformation
      $result= NULL;
      if ($proc) {
        $result= $proc->process($this->document, $this->params, FALSE);
      }
      $cwd && chdir($cwd);
      restore_error_handler();

      // Check result
      if (!$result) {
        throw(new TransformerException(
          "Transformation failed: [\n".implode("\n  ", $this->_errors)."\n]"
        ));
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
      if ($this->document) { with ($n= &$this->document->document_element); {
        $n && $n->unlink_node($n);
      }}
      $this->document= NULL;
    }
  } 
?>
