<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XML', 'xml.TransformerException', 'io.FileNotFoundException');
  
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
  class DomXSLProcessor extends XML {
    var 
      $processor    = NULL,
      $stylesheet   = array(),
      $document     = NULL,
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
      $this->processor= NULL;
    }

    /**
     * Set base directory
     *
     * @access  public
     * @param   string dir
     */
    function setBase($dir) {
      $this->_base= rtrim(realpath($dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
     * Set a scheme handler
     *
     * @access  public
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    function setSchemeHandler($defines) {
      // Not implemented in DOM
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
      $this->stylesheet= array(0, $this->_base.$file);
    }
    
    /**
     * Set XSL buffer
     *
     * @access  public
     * @param   string xsl the XSL as a string
     */
    function setXSLBuf($xsl) {
      $this->stylesheet= array(1, &$xsl);
    }

    /**
     * Set XML file
     *
     * @access  public
     * @param   string file file name
     */
    function setXMLFile($file) {
      $this->document= &domxml_open_file($this->_base.$file);
    }
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    function setXMLBuf($xml) {
      $this->document= &domxml_open_mem($xml);
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
     * Error handler callback
     *
     * @access  private
     * @param   int code
     * @param   string msg
     * @param   string file
     * @param   int line
     * @see     php://set_error_handler
     */
    function _traperror($code, $msg, $file, $line) {
      if (in_array(strtok($msg, ':'), array(
        'domxml_xslt_stylesheet()', 
        'domxml_xslt_stylesheet_file()',
        'process()',
      ))) {
        $this->_errors[]= strtok("\r\n");
        return;
      }

      __error($code, $msg, $file, $line);
    }

    /**
     * Run the XSL transformation
     *
     * @access  public
     * @return  bool success
     * @throws  xml.TransformerException
     */
    function run() {
      $cwd= FALSE;

      $this->_errors= array();
      set_error_handler(array(&$this, '_traperror'));

      // Get stylesheet
      switch ($this->stylesheet[0]) {
        case 0: 
          $proc= &domxml_xslt_stylesheet_file($this->stylesheet[1]); 
          break;

        case 1:
          if ($this->_base) {
            $cwd= getcwd();
            chdir($this->_base);
          }
          $proc= &domxml_xslt_stylesheet($this->stylesheet[1]);
          break;

        default:
          $proc= FALSE;
      }
      
      // Start transformation
      $result= NULL;
      if ($proc) {
        $result= &$proc->process($this->document, $this->params, FALSE);
      }
      $cwd && chdir($cwd);
      restore_error_handler();

      // Check result
      if (!$result) {
        return throw(new TransformerException(
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
    function output() {
      return $this->output;
    }

    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      if ($this->document) { with ($n= &$this->document->document_element); {
        $n && $n->unlink_node($n);
      }}
      $this->document= NULL;
    }
  }
?>
