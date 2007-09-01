<?php
/* This class is part of the XP framework
 *
 * $Id: XSLProcessor.class.php 9239 2007-01-10 22:06:44Z friebe $
 */

  namespace xml;

  uses(
    'xml.XML',
    'xml.TransformerException',
    'io.FileNotFoundException',
    'xml.IXSLProcessor'
  );
  
  /**
   * XSL Processor
   * 
   * Usage example [Transform two files]
   * <code>
   *   $proc= &new XSLProcessor();
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
   * @purpose  Transform XML/XSLT using PHPs builtin XSL functions
   * @ext      xslt
   * @see      http://www.gingerall.com - Sablotron
   * @deprecated Use DomXSLProcessor instead - Sablotron is unsupported in PHP5
   */
  class XSLProcessor extends XML implements IXSLProcessor {
    public 
      $processor    = NULL,
      $stylesheet   = array(),
      $buffer       = array(),
      $params       = array(),
      $output       = '';

    public
      $_base        = '';

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->processor= xslt_create();
    }
    
    /**
     * Error handler callback
     *
     * @param   resource parser
     * @param   int num
     * @param   int level
     * @param   array detail
     * @see     php://xslt_set_error_handler
     */
    public function _traperror($parser, $num, $level, $detail) {
      $message= sprintf(
        '%s %s #%d: %s', 
        $detail['module'], 
        $detail['msgtype'], 
        $detail['code'], 
        $detail['msg']
      );
      
      // Trigger errors so that messages get appended to the stack trace
      __error($num, $message, __FILE__, __LINE__);
      if (isset($detail['URI'])) {
        __error($num, 'URI: '.$detail['URI'].':'.$detail['line'], __FILE__, __LINE__);
      }
    }

    /**
     * Retrieve messages generate during processing.
     *
     * @return  string[]
     */
    public function getMessages() {
      return array();   // TBI
    }

    /**
     * Set a scheme handler
     *
     * @param   mixed callback
     * @see     php://xslt_set_scheme_handlers
     */
    public function setSchemeHandler($defines) {
      xslt_set_scheme_handlers($this->processor, $defines);
    }

    /**
     * Set base directory
     *
     * @param   string dir
     */
    public function setBase($dir) {
      $this->_base= rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
     * Set XSL file
     *
     * @param   string file file name
     * @throws  io.FileNotFoundException
     */
    public function setXSLFile($file) {
      if (!file_exists($this->_base.$file)) {
        throw(new io::FileNotFoundException($this->_base.$file.' not found'));
      }
      $this->stylesheet= array($this->_base.$file, NULL);
    }
    
    /**
     * Set XSL buffer
     *
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf($xsl) {
      $this->stylesheet= array('arg:/_xsl', $xsl);
    }

    /**
     * Set XML file
     *
     * @param   string file file name
     */
    public function setXMLFile($file) {
      $this->buffer= array($this->_base.$file, NULL);
    }
    
    /**
     * Set XML buffer
     *
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml) {
      $this->buffer= array('arg:/_xml', $xml);
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
     * Retrieve XSL transformation parameter
     *
     * @param   string name
     * @return  string value
     */
    public function getParam($name) {
      return $this->params[$name];
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
     * Run the XSL transformation
     *
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run($buffers= array()) {
      if (NULL != $this->buffer[1]) $buffers['/_xml']= $this->buffer[1];
      if (NULL != $this->stylesheet[1]) $buffers['/_xsl']= $this->stylesheet[1];
      
      xslt_set_error_handler($this->processor, array($this, '_traperror'));
      $this->output= xslt_process(
        $this->processor, 
        $this->buffer[0],
        $this->stylesheet[0],
        NULL,
        $buffers,
        $this->params
      );
      xslt_set_error_handler($this->processor, NULL);
      
      if (FALSE === $this->output) {
        throw(new TransformerException('Transformation failed'));
      }
      
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

    /**
     * Destructor
     *
     */
    public function __destruct() {
      if ($this->processor) {
        xslt_free($this->processor);
        $this->processor= NULL;
      }
    }
  } 
?>
