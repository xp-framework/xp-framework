<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'org.apache.HttpScriptletResponse',
    'org.apache.xml.OutputDocument',
    'xml.XSLProcessor'
  );
  
  /**
   * Wraps XML response
   *
   * Instead of writing directly to the client, use the addFormValue,
   * addFormResult or addFormError methods to access the resulting
   * XML document tree.
   *
   * @see org.apache.xml.OutputDocument
   * @see org.apache.HttpScriptletResponse  
   */
  class XMLScriptletResponse extends HttpScriptletResponse {
    var 
      $document,
      $processor,
      $stylesheet,
      $params,
      $page;
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->document= &new OutputDocument();
      $this->processor= &new XSLProcessor();
      parent::__construct();
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #addFormValue
     * @see     #addFormResult
     * @see     #addFormError
     */
    function write() {
      throw(new IllegalAccessException('Cannot write directly'));
    }
    
    /**
     * Overwritten method from parent class
     *
     * @access  public
     * @throws  IllegalAccessException
     * @see     #addFormValue
     * @see     #addFormResult
     * @see     #addFormError
     */
    function setContent() {
      throw(new IllegalAccessException('Cannot write directly'));
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
     * @access  public
     * @param   string name name
     * @return  &mixed val value
     */
    function &addFormValue($name, &$val) {
      if (!is_array($val)) $val= array($val);

      foreach (array_keys($val) as $k) {
        $v= &$val[$k];
        $c= &$this->document->formvalues->addChild(new Node(array(
          'name'        => 'param',
          'attribute'   => array(
            'name'            => $name.(is_int($k) ? '' : "[{$k}]"), 
            'xsi:type'        => 'xsd:'.gettype($v)
          )
        )));
        if (is_array($v)) {
          $c->fromArray($v, $c->name);
        } elseif (is_object($v)) {
          $c->fromObject($v, $c->name);
        } else {
          $c->setContent($v);
        }
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
     * @access  public
     * @param   string checker The class checking the input
     * @param   string type The error type
     * @param   string field default '*' The form field corresponding
     * @param   mixed info default NULL 
     * @return  bool FALSE
     */
    function addFormError($checker, $type, $field= '*', $info= NULL) {
      $e= &$this->document->formerrors->addChild(new Node(array(
        'name'      => 'error',
        'attribute' => array(
          'type'        => $type,
          'field'       => $field,
          'checker'     => $checker
        )
      )));
      if (is_array($info)) {
        $e->fromArray($info, $e->name);
      } elseif (is_object($v)) {
        $e->fromObject($info, $e->name);
      } elseif (!is_null($info)) {
        $e->setContent($info);
      }

      return FALSE;
    }
    
    /**
     * Add a child to the formresult node. You may add _any_ node
     * here since there is no special specification what do with
     * nodes besides formvalues and formerrors
     *
     * @access  public
     * @param   xml.Node node
     * @return  xml.Node added node
     * @throws  IllegalArgumentException if you try to add a node with
     *          the name "formerrors" or "formvalues"
     */
    function &addFormResult(&$node) {
      if (
        ('formerrors' == $node->name) ||
        ('formvalues' == $node->name)
      ) {
        return throw(new IllegalArgumentException($node->name.' not allowed here'));
      }
      return $this->document->formresult->addChild($node);
    }
    
    /**
     * Sets the absolute path to the XSL stylesheet
     *
     * @access  public
     * @param   string stylesheet
     */
    function setStylesheet($stylesheet) {
      $this->stylesheet= $stylesheet;
    }
    
    /**
     * Sets an XSL parameter
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setParam($name, $value) {
      $this->params['__'.$name]= $value;
    }
    
    /**
     * Retreives an XSL parameter by its name
     *
     * @access  public
     * @param   string name
     * @return  string value
     */
    function getParam($name) {
      return $this->params['__'.$name];
    }
    
    /**
     * Transorms the OutputDocument's XML and the stylesheet
     *
     * DOMXML/EXSLT variant LEAKS HUGE CHUNKS OF MEMORY!!!
     * <code>
     *   $doc= &domxml_open_mem(
     *     $this->document->getDeclaration()."\n".
     *     $this->document->getSource(FALSE)
     *   );
     *   $xsl= &domxml_xslt_stylesheet_file($this->stylesheet);
     *   $result= &$xsl->process($doc, $this->params, FALSE);
     *   $this->content= &$result->dump_mem();
     * </code>
     *
     * @throws  IllegalStateException if no stylesheet is set
     * @throws  FormatException if the transformation fails
     * @see     org.apache.HttpScriptletResponse#process
     */
    function process() {
      parent::process();
      if (empty($this->stylesheet)) {
        return throw(new IllegalStateException('No stylesheet set'));
      }

      $this->processor->setXSLFile($this->stylesheet);
      $this->processor->setParams($this->params);
      $this->processor->setXMLBuf(
        $this->document->getDeclaration()."\n".
        $this->document->getSource(FALSE)
      );
      
      // Transform XML/XSL
      if (!$this->processor->run()) {
        return throw(new FormatException(
          'xsl transformation of '.$this->stylesheet.' failed'
        ));
      }
      
      $this->content= &$this->processor->output();

      return TRUE;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->document->__destruct();
      $this->processor->__destruct();
      parent::__destruct();
    }
  }
?>
