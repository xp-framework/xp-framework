<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('XSL_VERSION_1_0', '1.0');
  define('XSL_NAMESPACE',   'http://www.w3.org/1999/XSL/Transform');

  uses('xml.Tree', 'xml.XslTemplate');

  /**
   * Represents an XSL stylesheet
   *
   * Usage example:
   * <code>
   *   uses('xml.Stylesheet');
   *
   *   $s= new Stylesheet();
   *   $s->setOutputMethod('text');
   *   $s->addImport('test.import.xsl');
   *   $s->addInclude('test.include.xsl');
   *   
   *   echo $s->getSource(INDENT_DEFAULT);
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.xml.StylesheetTest
   * @see      http://www.w3.org/TR/xslt XSL Transformations (XSLT) Version 1.0
   * @purpose  Wrapper class
   */
  class Stylesheet extends Tree {
     
    /**
     * Constructor
     *
     * @param   string version default XSL_VERSION_1_0
     */
    public function __construct($version= XSL_VERSION_1_0) {
      parent::__construct('xsl:stylesheet');
      
      // Add attributes for root node
      $this->root->setAttribute('version', $version);
      $this->root->setAttribute('xmlns:xsl', XSL_NAMESPACE);
    }

    /**
     * Set output method, indentation and encoding.
     *
     * Note: Output encoding is set to document encoding if not 
     * specified otherwise!
     *
     * @param   string method
     * @param   bool indent default TRUE
     * @param   string encoding default NULL
     */
    public function setOutputMethod($method, $indent= TRUE, $encoding= NULL) {
      with ($n= $this->root->addChild(new Node('xsl:output'))); {
        $n->setAttribute('method', $method);
        $n->setAttribute('encoding', $encoding ? $encoding : $this->getEncoding());
        $n->setAttribute('indent', $indent ? 'yes' : 'no');
      }
    }

    /**
     * Set output method, indentation and encoding and return this stylesheet.
     *
     * Note: Output encoding is set to document encoding if not 
     * specified otherwise!
     *
     * @param   string method
     * @param   bool indent default TRUE
     * @param   string encoding default NULL
     * @return  xml.Stylesheet this
     */
    public function withOutputMethod($method, $indent= TRUE, $encoding= NULL) {
      $this->setOutputMethod($method, $indent, $encoding);
      return $this;
    }

    /**
     * Add an import
     *
     * @param   string import
     * @return  xml.Node the added node
     */
    public function addImport($import) {
      with ($n= $this->root->addChild(new Node('xsl:import'))); {
        $n->setAttribute('href', $import);
      }
      return $n;
    }

    /**
     * Add an import and return this stylesheet.
     *
     * @param   string import
     * @return  xml.Stylesheet this
     */
    public function withImport($import) {
      $this->addImport($import);
      return $this;
    }

    /**
     * Add an include
     *
     * @param   string include
     * @return  xml.Node the added node
     */
    public function addInclude($include) {
      with ($n= $this->root->addChild(new Node('xsl:include'))); {
        $n->setAttribute('href', $include);
      }
      return $n;
    }

    /**
     * Add an include and return this stylesheet.
     *
     * @param   string include
     * @return  xml.Stylesheet this
     */
    public function withInclude($include) {
      $this->addInclude($include);
      return $this;
    }

    /**
     * Add a parameter
     *
     * @param   string import
     * @return  xml.Node the added node
     */
    public function addParam($name) {
      with ($n= $this->root->addChild(new Node('xsl:param'))); {
        $n->setAttribute('name', $name);
      }
      return $n;
    }

    /**
     * Add a parameter and return this stylesheet.
     *
     * @param   string import
     * @return  xml.Stylesheet this
     */
    public function withParam($name) {
      $this->addParam($name);
      return $this;
    }

    /**
     * Add a variable
     *
     * @param   string name
     * @return  xml.Node the added node
     */
    public function addVariable($name) {
      with ($n= $this->root->addChild(new Node('xsl:variable'))); {
        $n->setAttribute('name', $name);
      }
      return $n;
    }

    /**
     * Add a variable and return this stylesheet.
     *
     * @param   string name
     * @return  xml.Stylesheet this
     */
    public function withVariable($name) {
      $this->addVariable($name);
      return $this;
    }

    /**
     * Add a template
     *
     * @param   xml.XslTemplate t
     * @return  xml.XslTemplate the added template
     */
    public function addTemplate(XslTemplate $t) {
      $this->root->addChild($t);
      return $t;
    }

    /**
     * Add a template and return this stylesheet.
     *
     * @param   xml.XslTemplate t
     * @return  xml.Stylesheet this
     */
    public function withTemplate(XslTemplate $t) {
      $this->root->addChild($t);
      return $this;
    }
    
    /**
     * Construct a stylesheet from a string
     *
     * @param   string string
     * @return  xml.Stylesheet
     */
    public static function fromString($string, $c= __CLASS__) {
      return parent::fromString($string, $c);
    }


    /**
     * Construct a stylesheet from a file
     *
     * @param   xml.File file
     * @return  xml.Stylesheet
     */
    public static function fromFile($file, $c= __CLASS__) {
      return parent::fromFile($file, $c);
    }
  }
?>
