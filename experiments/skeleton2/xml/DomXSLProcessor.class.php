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
      $processor    = NULL,
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
      $this->_base= rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
     */
    public function setXSLFile($file) {
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
      $this->document= DomDocument::load($this->_base.$file);
    }
    
    /**
     * Set XML buffer
     *
     * @access  public
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml) {
      $this->document= DomDocument::loadXML($xml);
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
      $cwd= FALSE;

      // Get stylesheet
      $proc= new XSLTProcessor();
      foreach ($this->params as $key => $val) {
        $proc->setParameter(NULL, $key, $val);
      }
      switch ($this->stylesheet[0]) {
        case 0:
          $proc->importStyleSheet(DomDocument::load($this->stylesheet[1]));
          break;

        case 1:
          if ($this->_base) {
            $cwd= getcwd();
            chdir($this->_base);
          }
          $proc->importStyleSheet(DomDocument::loadXML($this->stylesheet[1]));
          break;

        default:
          $proc= xp::$null;
      }
      
      // Transform this
      $this->output= $proc->transformToXML($this->document);

      delete($proc);
      if (!$this->output) {
        echo '<xmp>'; var_dump(xp::$errors);
        throw (new TransformerException('Transformation failed'));
      }
      
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
      $this->document= NULL;
      parent::__destruct();
    }
  }
?>
