<?php
/*
 * $Id$
 *
 * Diese Klasse ist Bestandteil des XP-Frameworks
 * (c) 2001 Timm Friebe, Schlund+Partner AG
 *
 * @see http://doku.elite.schlund.de/projekte/xp/skeleton/
 *
 */

  import('xml.XML');
  import('xml.XSLProcessor');
  
  class XPath extends XML {
    var $_proc= NULL;
    
    function __construct($params= NULL) {
      XML::__construct();
      $this->_proc= new XSLProcessor();
    }
    
    function _exprXSL($expression) {
      return (
        '<?xml version="1.0" encoding="iso-8859-1"?>'.
        '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> '.
        '  <xsl:output method="xml" encoding="iso-8859-1" omit-xml-declaration="yes" indent="no"/>'.
        '  <xsl:template match="xsl:buf">'.
        '    <xsl:apply-templates select="document(\'arg:/_doc\')'.$expression.'"/>'.
        '  </xsl:template>'.
        '  <xsl:template match="@*">'.
        '    <xsl:value-of select="."/>'.
        '  </xsl:template>'.
        '  <xsl:template match="*">'.
        '    <xsl:copy>'.
        '      <xsl:copy-of select="@*"/>'.
        '      <xsl:apply-templates/>'.
        '    </xsl:copy>'.
        '  </xsl:template>'.
        '</xsl:stylesheet>'
      );
    }
    
    function setContext($xml) {
      $this->context= $xml;
    }
    
    function setContextFile($filename) {
      $this->context= implode('', file($filename));
    }
    
    function evaluate($expression) {
      $this->_proc->setXMLBuf('<xsl:buf xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
      $this->_proc->setXSLBuf($this->_exprXSL($expression));
      
      return $this->_proc->run(array(
        '/_doc' => $this->context
      )) ? $this->_proc->output() : FALSE;
    }
    
    /**
     * Destructor
     */
    function __destruct() {
      $this->_proc->__destruct();
      XML::__destruct();
    }
  }
?>
