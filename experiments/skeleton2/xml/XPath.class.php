<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.XML',
    'xml.XSLProcessor'
  );

  /**
   * XPath class
   *
   * @ext   xslt
   */
  class XPath extends XML {
    protected
      $_proc= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      $this->_proc= new XSLProcessor();
    }
    
    protected function _exprXSL($expression) {
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
    
    public function setContext($xml) {
      $this->context= $xml;
    }
    
    public function setContextFile($filename) {
      $this->context= implode('', file($filename));
    }
    
    public function evaluate($expression) {
      $this->_proc->setXMLBuf('<xsl:buf xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
      $this->_proc->setXSLBuf(self::_exprXSL($expression));
      
      return $this->_proc->run(array(
        '/_doc' => $this->context
      )) ? $this->_proc->output() : FALSE;
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
      unset($this->_proc);
    }
  }
?>
