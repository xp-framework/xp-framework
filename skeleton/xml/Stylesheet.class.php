<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('XSL_VERSION_1_0', '1.0');

  uses('xml.Node');

  /**
   * Stylesheet
   *
   * @purpose  Represents an xsl stylesheet
   */
  class Stylesheet extends Node {
    var
      $version  = XSL_VERSION_1_0;
     
    /**
     * __construct
     *
     * @access  public
     * @param   const version
     */
    function __construct($version= XSL_VERSION_1_0) {
      parent::__construct(sprintf(
        'xsl:stylesheet',
        $version
      ));
      $this->version= $version;
      
      // Add attributes for root node
      $this->setAttribute('version', $version);
      $this->setAttribute('xmlns:xsl', 'http://www.w3.org/1999/XSL/Transform');
    }

    /**
     * Set outputmethod
     *
     * @access  public
     * @param   string method
     */
    function setOutputmethod($method= 'xml') {
      with ($n= &new Node('xsl:output method')); {
        $n->setAttribute('method', $method);
        $n->setAttribute('version', $this->version);
        $n->setAttribute('encoding', $this->getEncoding());
        $n->setAttribute('indent', 'yes');
      }
      $this->addChild($n);
    }

    /**
     * Add an import param
     *
     * @access  public
     * @param   array import
     */
    function addImport($import) {
      with ($n= &new Node('xsl:import')); {
        $n->setAttribute('href', $import);
      }
      $this->addChild($n);
    }

    /**
     * Add an include param
     *
     * @access  public
     * @param   array include
     */
    function addInclude($include) {
      with ($n= &new Node('xsl:include')); {
        $n->setAttribute('href', $include);
        $this->addChild($n);
      }
    }
  }
?>
