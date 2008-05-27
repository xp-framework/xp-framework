<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'xml.TransformerException',
    'io.FileNotFoundException',
    'xml.IXSLProcessor',
    'xml.XSLCallback',
    'xml.xslt.XSLDateCallback',
    'xml.xslt.XSLStringCallback'
  );
  
  /**
   * XSL Processor using DomXML
   * 
   * Usage example [Transform two files]
   * <code>
   *   $proc= new DomXSLProcessor();
   *   $proc->setXSLFile('test.xsl');
   *   $proc->setXMLFile('test.xml');
   *   
   *   try {
   *     $proc->run();
   *   } catch(TransformerException $e) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *
   *   var_dump($proc->output());
   * </code>
   *
   * @purpose  Transform XML/XSLT using PHPs XSLT functions
   * @ext      xslt
   * @test     xp://net.xp_framework.unittest.xml.DomXslProcessorTest
   */
  class DomXSLProcessor extends Object implements IXSLProcessor {
    public 
      $processor      = NULL,
      $stylesheet     = NULL,
      $document       = NULL,
      $params         = array(),
      $output         = '',
      $outputEncoding = '',
      $baseURI        = '';

    public
      $_instances   = array(),
      $_base        = '';

    static function __static() {
      libxml_use_internal_errors(TRUE);
    }
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->registerInstance('xp.date', new XSLDateCallback());
      $this->registerInstance('xp.string', new XSLStringCallback());
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
      if (!file_exists($this->_base.urldecode($file)))
        throw new FileNotFoundException($this->_base.$file.' not found');

      libxml_get_last_error() && libxml_clear_errors();

      $this->stylesheet= new DOMDocument();
      $this->stylesheet->load($this->_base.urldecode($file));
      $this->baseURI= $this->_base.$file;

      $this->_checkErrors($file);
    }
    
    /**
     * Set XSL buffer
     *
     * @param   string xsl the XSL as a string
     */
    public function setXSLBuf($xsl) {
      libxml_get_last_error() && libxml_clear_errors();

      $this->stylesheet= new DOMDocument();
      $this->stylesheet->loadXML($xsl);
      strlen($this->_base) && $this->stylesheet->documentURI= $this->_base;
      $this->baseURI= $this->_base.':string';
      
      $this->_checkErrors($xsl);
    }

    /**
     * Set XSL from a tree
     *
     * @param   xml.Tree xsl
     */
    public function setXSLTree(Tree $xsl) {
      libxml_get_last_error() && libxml_clear_errors();

      $this->stylesheet= new DOMDocument();
      $this->stylesheet->loadXML($xsl->getDeclaration().$xsl->getSource(INDENT_NONE));
      strlen($this->_base) && $this->stylesheet->documentURI= $this->_base;
      $this->baseURI= $this->_base.':tree';
      
      $this->_checkErrors($xsl);
    }

    /**
     * Set XML file
     *
     * @param   string file file name
     */
    public function setXMLFile($file) {
      if (!file_exists($this->_base.urldecode($file))) {
        throw new FileNotFoundException($this->_base.$file.' not found');
      }
      
      libxml_get_last_error() && libxml_clear_errors();

      $this->document= new DOMDocument();
      $this->document->load(urldecode($file));

      $this->_checkErrors($file);
    }
    
    /**
     * Set XML buffer
     *
     * @param   string xml the XML as a string
     */
    public function setXMLBuf($xml) {
      libxml_get_last_error() && libxml_clear_errors();

      $this->document= new DOMDocument();
      $this->document->loadXML($xml);

      $this->_checkErrors($xml);
    }

    /**
     * Set XML from a tree
     *
     * @param   xml.Tree xml
     */
    public function setXMLTree(Tree $xml) {
      libxml_get_last_error() && libxml_clear_errors();

      $this->document= new DOMDocument();
      $this->document->loadXML($xml->getDeclaration().$xml->getSource(INDENT_NONE));

      $this->_checkErrors($xml);
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
      return libxml_get_last_error();
    }
    
    /**
     * Register object instance under defined name
     * for access from XSL callbacks.
     *
     * @param   string name
     * @param   lang.Object instance
     */
    function registerInstance($name, $instance) {
      $this->_instances[$name]= $instance;
    }
    
    /**
     * Determine output encoding. 
     *
     * Note: This is a workaround for the problem that when calling:
     * <code>
     *   $result= $processor->transformToXML($xml);
     * </code>
     * the charset of $result (a string) is unknown. 
     *
     * When using:
     * <code>
     *   $result= $processor->transformToDoc($xml);   
     * </code>
     * the charset can be retrieved by having a look at $result's actualEncoding 
     * property, but then again the output method is neglected and we do not 
     * know what to save the result as (saveHTML? saveXML?)
     *
     * Thus we manually check for xsl:output in the stylesheet and all its 
     * includes and imports (recursively!) - the overhead is typically about 
     * 8 to 10 milliseconds.
     *
     * @param   php.DOMElement root
     * @param   string base
     * @return  string encoding or NULL if no user-defined encoding could be found
     * @throws  xml.TransformerException in case an include or import cannot be found
     */
    protected function determineOutputEncoding(DOMElement $root, $base) {
      static $xsltNs= 'http://www.w3.org/1999/XSL/Transform';

      // Check whether a xsl:output-method tag exists and if it has an 
      // encoding attribute - in this case, we've one.
      if ($output= $root->getElementsByTagNameNS($xsltNs, 'output')->item(0)) {
        if ($e= $output->getAttribute('encoding')) return $e;
      }
      
      $baseDir= dirname($base);

      // Check xsl:include nodes
      foreach ($root->getElementsByTagNameNS($xsltNs, 'include') as $include) {
        $dom= new DOMDocument();
        $href= $include->getAttribute('href');
        if (!('/' === $href{0} || strstr($href, '://') || ':/' === substr($href, 1, 2))) {
          $href= $baseDir.'/'.$href;    // Relative
        }
        if (!($dom->load(urldecode($href)))) {
          throw new TransformerException('Cannot find include '.$href."\n at ".$base);
        }
        if ($e= $this->determineOutputEncoding($dom->documentElement, $href)) return $e;
      }

      // Check xsl:import nodes
      foreach ($root->getElementsByTagNameNS($xsltNs, 'import') as $import) {
        $dom= new DOMDocument();
        $href= $import->getAttribute('href');
        if (!('/' === $href{0} || strstr($href, '://') || ':/' === substr($href, 1, 2))) {
          $href= $baseDir.'/'.$href;    // Relative
        }
        if (!($dom->load(urldecode($href)))) {
          throw new TransformerException('Cannot find import '.$href."\n at ".$base);
        }
        if ($e= $this->determineOutputEncoding($dom->documentElement, $href)) return $e;
      }
      
      // Cannot determine encoding
      return NULL;
    }

    /**
     * Run the XSL transformation
     *
     * @return  bool success
     * @throws  xml.TransformerException
     */
    public function run() {
      libxml_get_last_error() && libxml_clear_errors();
      
      $this->processor= new XSLTProcessor();
      $this->processor->importStyleSheet($this->stylesheet);
      $this->processor->setParameter('', $this->params);

      // If we have registered instances, register them in XSLCallback
      if (sizeof($this->_instances)) {
        $cb= XSLCallback::getInstance();
        foreach ($this->_instances as $name => $instance) {
          $cb->registerInstance($name, $instance);
        }
      }
      $this->processor->registerPHPFunctions(array('XSLCallback::invoke'));
      if (NULL === ($this->outputEncoding= $this->determineOutputEncoding(
        $this->stylesheet->documentElement,
        $this->baseURI
      ))) {
        $this->outputEncoding= 'utf-8';   // Use default
      }
      
      // Start transformation
      $this->output= $this->processor->transformToXML($this->document);
      
      
      // Perform cleanup when necessary (free singleton for further use)
      sizeof($this->_instances) && XSLCallback::getInstance()->clearInstances();
      
      // Check for transformation errors
      if (FALSE === $this->output) {
      
        // Check for errors, also non-fatal errors, otherwise indicate unknown
        // transformation error
        $this->_checkErrors(NULL, TRUE);
        throw new TransformerException('Unknown XSL transformation error while transforming '.$this->baseURI);
      }
      
      // Check for left-over errors that did not make the transformation fail
      $this->_checkErrors('<transformation>');
      return TRUE;
    }
    
    /**
     * Check for XML/XSLT errors and throw exceptions accordingly.
     *
     * In case fatal is TRUE, any libxml error will be treated as a
     * fatal one, resulting in an exception.
     *
     *
     * @param   string source default NULL
     * @param   bool fatal default FALSE
     * @throws  xml.TransformerException in case an XML error has occurred
     */
    protected function _checkErrors($source= NULL, $fatal= FALSE) {
      if (sizeof($errors= libxml_get_errors())) {
        libxml_clear_errors();
        $message= '';
        
        foreach ($errors as $error) {
          if (LIBXML_ERR_FATAL == $error->level) $fatal= TRUE;
          
          $message.= sprintf(
            "  #%d: %s\n  at %s, line %d, column %d\n",
            $error->code,
            trim($error->message, " \n"),
            strlen($error->file) ? $error->file : xp::stringOf($source),
            $error->line,
            $error->column
          );
        }
        
        if ($fatal) throw new TransformerException(
          'XSL Transformation error: '.trim($message, " \n")
        );
      }
    }

    /**
     * Retrieve the transformation's result
     *
     * @return  string
     */
    public function output() {
      return (string)$this->output;
    }

    /**
     * Retrieve the transformation's result's encoding
     *
     * @return  string
     */
    public function outputEncoding() {
      return $this->outputEncoding;
    }
  }
?>
