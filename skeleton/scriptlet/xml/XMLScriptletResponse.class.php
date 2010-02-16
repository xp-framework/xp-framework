<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'scriptlet.HttpScriptletResponse',
    'scriptlet.xml.OutputDocument',
    'xml.IXSLProcessor',
    'peer.http.HttpConstants'
  );
  
  // Deprecated
  define('XSLT_BUFFER', 0x0000);
  define('XSLT_FILE',   0x0001);
  define('XSLT_TREE',   0x0002);
  
  /**
   * Wraps XML response
   *
   * Instead of writing directly to the client, use the addFormValue,
   * addFormResult or addFormError methods to access the resulting
   * XML document tree.
   *
   * @see      xp://scriptlet.xml.OutputDocument
   * @see      xp://scriptlet.HttpScriptletResponse  
   * @purpose  Scriptlet response wrapper
   */
  class XMLScriptletResponse extends HttpScriptletResponse {
    const
      XSLT_BUFFER   = 0x0000,
      XSLT_FILE     = 0x0001,
      XSLT_TREE     = 0x0002;

    public
      $document     = NULL,
      $processor    = NULL,
      $params       = array();
    
    public
      $_processed   = TRUE,
      $_stylesheet  = array();
    
    /**
     * Constructor
     *
     * @param   xml.IXSLProcessor processor
     */
    public function __construct($processor= NULL) {
      $this->processor= $processor;
      $this->document= new OutputDocument();
    }

    /**
     * Set Processor
     *
     * @param   xml.IXSLProcessor processor
     */
    public function setProcessor($processor) {
      $this->processor= $processor;
    }

    /**
     * Get Processor
     *
     * @return  xml.IXSLProcessor processor
     */
    public function getProcessor() {
      return $this->processor;
    }

    /**
     * Set whether this document needs to be processed
     *
     * @param   bool processed
     */
    public function setProcessed($processed) {
      $this->_processed= $processed;
    }

    /**
     * Overwritten method from parent class
     *
     * @param   string s string to add to the content
     * @throws  lang.IllegalAccessException in case processing is requested
     */
    public function write($s) {
      if ($this->_processed) {
        throw new IllegalAccessException('Cannot write directly');
      }
      parent::write($s);
    }

    /**
     * Overwritten method from parent class
     *
     * @param   string content Content
     * @throws  lang.IllegalAccessException
     */
    public function setContent($content) {
      if ($this->_processed) {
        throw new IllegalAccessException('Cannot write directly');
      }
      parent::setContent($content);
    }
    
    /**
     * Add a child to the formvalues node. The XML representation
     * is probably best shown with a couple of examples:
     *
     * Example: a string
     * <xmp>
     *   <param name="__form" xsi:type="xsd:string">new</param>
     * </xmp>
     *
     * Example: an associative array
     * <xmp>
     *   <param name="data[domain]" xsi:type="xsd:string">thekidabc</param>
     *   <param name="data[tld]" xsi:type="xsd:string">de</param>
     * </xmp>
     *
     * Example: an object
     * <xmp>
     *   <param name="faxnumber" xsi:type="xsd:object">
     *     <pre>721</pre>
     *     <number>1234567</number>
     *     <lcode>+49</lcode>
     *   </param>
     * </xmp>     
     *
     * @param   string name name
     * @param   var val
     */
    public function addFormValue($name, $values) {
      if (!is_array($values)) $values= array($values);

      foreach ($values as $k => $val) {
        try {
          if (is_array($val)) {
            $c= Node::fromArray($val, 'param');
          } else if (is_object($val)) {
            $c= Node::fromObject($val, 'param');
          } else {
            $c= new Node('param', $val);
          }
          $c->attribute['name']= $name.(is_int($k) ? '' : '['.$k.']');
          $c->attribute['xsi:type']= 'xsd:'.gettype($val);
        } catch (XMLFormatException $e) {
        
          // An XMLFormatException indicates data we have received on-wire
          // does not conform to XML rules - eg. contains characters that are
          // not allowed within XML documents. As on-wire data is beyond the 
          // classes control, this must be handled to prevent application 
          // breakage.
          // Passing special XML characters such as < or & will not fall into this
          // block - they'll just be converted to their counterpart XML entities.
          $c= new Node('param', NULL, array(
            'name'      => $name,
            'xsi:type'  => 'xsd:null',
            'error'     => 'formaterror'
          ));
        }
        $this->document->formvalues->addChild($c);
      }
    }

    /**
     * Adds an error. The XML representation will look like this:
     * <xmp>
     *   <error
     *    checker="foo.bar.wrapper.MyLoginDataChecker"
     *    type="user_nonexistant"
     *    field="username"                    
     *   />                                                 
     * </xmp>
     *
     * @param   string checker The class checking the input
     * @param   string type The error type
     * @param   string field default '*' The form field corresponding
     * @param   var info default NULL 
     * @return  bool FALSE
     */
    public function addFormError($checker, $type, $field= '*', $info= NULL) {
      if (is_array($info)) {
        $c= Node::fromArray($info, 'error');
      } else if (is_object($info)) {
        $c= Node::fromObject($info, 'error');
      } else {
        $c= new Node('error', $info);
      }
      $c->attribute= array(
        'type'        => $type,
        'field'       => $field,
        'checker'     => $checker
      );
      $this->document->formerrors->addChild($c);
      
      return FALSE;
    }
    
    /**
     * Add a child to the formresult node. You may add _any_ node
     * here since there is no special specification what do with
     * nodes besides formvalues and formerrors
     *
     * @param   xml.Node node
     * @return  xml.Node added node
     * @throws  lang.IllegalArgumentException
     */
    public function addFormResult($node) {
      if (
        ('formerrors' == $node->name) ||
        ('formvalues' == $node->name)
      ) {
        throw new IllegalArgumentException($node->name.' not allowed here');
      }
      return $this->document->formresult->addChild($node);
    }
    
    /**
     * Sets the absolute path to the XSL stylesheet
     *
     * @param   string stylesheet
     * @param   int type default XSLT_FILE
     */
    public function setStylesheet($stylesheet, $type= XSLT_FILE) {
      $this->_stylesheet= array($type, $stylesheet);
    }

    /**
     * Retrieves whether a stylesheet has been set
     *
     * @return  bool
     */
    public function hasStylesheet() {
      return !empty($this->_stylesheet);
    }
    
    /**
     * Sets an XSL parameter
     *
     * @param   string name
     * @param   string value
     */
    public function setParam($name, $value) {
      $this->params['__'.$name]= $value;
    }
    
    /**
     * Retrieves an XSL parameter by its name
     *
     * @param   string name
     * @return  string value
     */
    public function getParam($name) {
      return $this->params['__'.$name];
    }
    
    /**
     * Forward to another state (optionally with query string and fraction)
     *
     * @param   string state
     * @param   string query default NULL the query string without the leading "?"
     * @param   string fraction default NULL the fraction without the leading "#"
     */
    public function forwardTo($state, $query= NULL, $fraction= NULL) {
      
      // Forward based on current URL
      $uri= clone $this->uri;
      
      // Construct new URL
      $uri->setStateName($state);
      $uri->setQuery($query);
      $uri->setFragment($fraction);
      
      // Redirect
      $this->sendRedirect($uri->getURL());
    }
    
    /**
     * Transforms the OutputDocument's XML and the stylesheet
     *
     * @throws  lang.IllegalStateException if no stylesheet is set
     * @throws  scriptlet.HttpScriptletException if the transformation fails
     * @see     xp://scriptlet.HttpScriptletResponse#process
     */
    public function process() {
      if (!$this->_processed) return FALSE;

      switch ($this->_stylesheet[0]) {
        case self::XSLT_FILE:
          try {
            $this->processor->setXSLFile($this->_stylesheet[1]);
          } catch (FileNotFoundException $e) {
            throw new HttpScriptletException($e->getMessage(), HttpConstants::STATUS_NOT_FOUND);
          }
          break;
          
        case self::XSLT_BUFFER:
          $this->processor->setXSLBuf($this->_stylesheet[1]);
          break;

        case self::XSLT_TREE:
          $this->processor->setXSLTree($this->_stylesheet[1]);
          break;
        
        default:
          throw new IllegalStateException(
            'Unknown type ('.$this->_stylesheet[0].') for stylesheet'
          );
      }
      
      $this->processor->setParams($this->params);
      $this->processor->setXMLTree($this->document);
      
      // Transform XML/XSL
      try {
        $this->processor->run();
      } catch (TransformerException $e) {
        throw new HttpScriptletException($e->getMessage(), HttpConstants::STATUS_INTERNAL_SERVER_ERROR);
      }
      
      $this->content= $this->processor->output();
      $this->setContentType('text/html; charset='.$this->processor->outputEncoding());
      return TRUE;
    }
    
    /**
     * Destructor
     *
     */
    public function __destruct() {
      delete($this->document);
      delete($this->processor);
    }
  }
?>
