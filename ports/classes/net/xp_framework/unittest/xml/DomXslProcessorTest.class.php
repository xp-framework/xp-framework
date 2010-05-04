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
   * @see       xp://xml.DomXSLProcessor
   * @ext       dom
   * @ext       xsl
   * @purpose   Unit Test
   */
  class DomXslProcessorTest extends AbstractProcessorTest {

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @return  string[]
     */
    public function neededExtension() { 
      return array('dom', 'xsl');
    }
  
    /**
     * Returns the XSL processor instance to be used
     *
     * @return  xml.IXSLProcessor
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
    
    /**
     * Callback method without xslmethod annotation
     *
     * @return  string
     */
    public function nonXslMethod() {
      return '@@ILLEGAL@@';
    }
    
    /**
     * Callback method
     *
     * @return  string
     */
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
    #[@test, @expect('lang.ElementNotFoundException')]
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
            <xsl:value-of select="php:function(\'XSLCallback::invoke\', \'notregistered\', \'irrelevant\')"/>
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
     * Test that errors in libxml error stack do not affect XSL processor
     * instances created before the error occurs
     *
     */
    #[@test]
    public function errorStackDoesNotAffectProcessorCreatedBefore() {
      $i= $this->processorInstance();
    
      // Fill up error stack artificially
      $doc= new DOMDocument();
      $doc->loadXML('@@MALFORMED@@');
      
      // Should work
      $i->setXMLBuf('<document/>');
      $i->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/">
            <b>Hello</b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $i->run();
      $this->assertXmlEquals('<b>Hello</b>', $i->output());
    }

    /**
     * Test that errors in libxml error stack do not affect XSL processor
     * instances created after the error occurs
     *
     */
    #[@test]
    public function errorStackDoesNotAffectProcessorCreatedAfter() {
    
      // Fill up error stack artificially
      $doc= new DOMDocument();
      $doc->loadXML('@@MALFORMED@@');
      
      // Should work
      $i= $this->processorInstance();
      $i->setXMLBuf('<document/>');
      $i->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/">
            <b>Hello</b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $i->run();
      $this->assertXmlEquals('<b>Hello</b>', $i->output());
    }

    /**
     * Test that cleared errors in libxml error stack do not affect 
     * errors occurring within a transformation
     *
     */
    #[@test]
    public function errorStackDoesNotAffectErrorHandling() {
    
      // Fill up error stack artificially
      $doc= new DOMDocument();
      $doc->loadXML('@@MALFORMED@@');
      
      // Should work
      $i= $this->processorInstance();
      try {
        $i->setXMLBuf('<document>&nbsp;</document>');
        $this->fail('Malformed XML did not trigger exception');
      } catch (TransformerException $e) {
        $this->assertTrue((bool)strstr($e->getMessage(), "Entity 'nbsp' not defined"));
      }
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function defaultCallbacks() {

      // Should work
      $this->processor->setXMLBuf('<document><string>lower string</string></document>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0"
         xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
         xmlns:php="http://php.net/xsl"
         exclude-result-prefixes="php"
        >
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/document">
            <i><xsl:value-of select="php:function(\'XSLCallback::invoke\', \'xp.string\', \'strtoupper\', string(string))"/></i>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertXmlEquals('<i>LOWER STRING</i>', $this->processor->output());
    }
    
  }
?>
