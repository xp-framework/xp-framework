<?php
/* This class is part of the XP framework
 *
 * $Id: Stylesheet.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace xml;

  define('XSL_VERSION_1_0', '1.0');
  define('XSL_NAMESPACE',   'http://www.w3.org/1999/XSL/Transform');

  uses('xml.Tree');

  /**
   * Represents an XSL stylesheet
   *
   * Usage example:
   * <code>
   *   uses('xml.Stylesheet');
   *
   *   $s= &new Stylesheet();
   *   $s->setOutputMethod('text');
   *   $s->addImport('test.import.xsl');
   *   $s->addInclude('test.include.xsl');
   *   
   *   echo $s->getSource(INDENT_DEFAULT);
   * </code>
   *
   * @see      http://www.w3.org/TR/xslt XSL Transformations (XSLT) Version 1.0
   * @purpose  Wrapper class
   */
  class Stylesheet extends Tree {
     
    /**
     * Constructor
     *
     * @param   string version default XSL_VERSION_1_0
     */
    public function __construct($version= ) {
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
    public function setOutputMethod($method, $indent= , $encoding= ) {
      with ($n= $this->root->addChild(new Node('xsl:output'))); {
        $n->setAttribute('method', $method);
        $n->setAttribute('encoding', $encoding ? $encoding : $this->getEncoding());
        $n->setAttribute('indent', $indent ? 'yes' : 'no');
      }
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
     * Add a variable
     *
     * @param   string import
     * @return  xml.Node the added node
     */
    public function addVariable($name) {
      with ($n= $this->root->addChild(new Node('xsl:variable'))); {
        $n->setAttribute('name', $name);
      }
      return $n;
    }
    
    /**
     * Construct a stylesheet from a string
     *
     * @param   string string
     * @return  xml.Stylesheet
     */
    public static function fromString($string) {
      return parent::fromString($string, __CLASS__);
    }


    /**
     * Construct a stylesheet from a file
     *
     * @param   xml.File file
     * @return  xml.Stylesheet
     */
    public static function fromFile($file) {
      return parent::fromFile($file, __CLASS__);
    }
  }
?>
