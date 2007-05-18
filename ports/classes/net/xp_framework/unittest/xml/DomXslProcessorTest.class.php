<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'xml.DomXSLProcessor',
    'net.xp_framework.unittest.xml.AbstractProcessorTest'
  );

  /**
   * ProcessorTest implementation that tests the DomXSL processor
   *
   * @see      xp://xml.DomXSLProcessor
   * @ext      domxml
   * @purpose  Unit Test
   */
  class DomXslProcessorTest extends AbstractProcessorTest {

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @return  string
     */
    public function neededExtension() { 
      return 'dom';
    }
  
    /**
     * Returns the XSL processor instance to be used
     *
     * @return  &xml.IXSLProcessor
     */
    public function processorInstance() {
      return new DomXSLProcessor();
    }

    /**
     * Returns the XSL processor's default output charset
     *
     * @return  string
     */
    public function processorCharset() { 
      return 'utf-8';
    }
    
    public function nonXslMethod() {
      return '@@ILLEGAL@@';
    }
    
    #[@xslmethod]
    public function XslMethod() {
      return '@@SUCCESS@@';
    }
    
    /**
     * Test 
     *
     */
    #[@test]
    public function callXslHook() {
      $this->processor->registerInstance('proc', $this);
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXslBuf('<?xml version="1.0"?>
        <xsl:stylesheet
         version="1.0"
         xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
         xmlns:php="http://php.net/xsl"
        >
          <xsl:template match="/">
            <xsl:value-of select="php:function(\'XSLCallback::invoke\', \'proc\', \'XslMethod\')"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callNonXslHook() {
      $this->processor->registerInstance('proc', $this);
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXslBuf('<?xml version="1.0"?>
        <xsl:stylesheet
         version="1.0"
         xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
         xmlns:php="http://php.net/xsl"
        >
          <xsl:template match="/">
            <xsl:value-of select="php:function(\'XSLCallback::invoke\', \'proc\', \'nonXslMethod\')"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function callNonRegisteredInstance() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXslBuf('<?xml version="1.0"?>
        <xsl:stylesheet
         version="1.0"
         xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
         xmlns:php="http://php.net/xsl"
        >
          <xsl:template match="/">
            <xsl:value-of select="php:function(\'XSLCallback::invoke\', \'notregistered\')"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }
    
    /**
     * Test error handling
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function malformedXML() {
      $this->processor->setXMLBuf('@@MALFORMED@@');
    }
    
    /**
     * Test error handling
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function malformedXSL() {
      $this->processor->setXSLBuf('@@MALFORMED@@');
    }
    
    /**
     * Test that errors in libxml error stack are caught at first
     * possible point - in the constructor. Make process bail out as
     * early as possible to make error trackdown more easy.
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function preventErrorLeaking() {
    
      // Fill up error stack artificially
      $doc= new DOMDocument();
      $doc->loadXML('@@MALFORMED@@');
      
      // Should give an exception
      $this->processorInstance();
    }
  }
?>
