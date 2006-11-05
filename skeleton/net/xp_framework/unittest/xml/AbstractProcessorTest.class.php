<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('util.profiling.unittest.TestCase');

  /**
   * Test XSL processor
   *
   * @see      xp://xml.IXSLProcessor
   * @purpose  Unit Test
   */
  class AbstractProcessorTest extends TestCase {
    var
      $processor      = NULL,
      $xmlDeclaration = '';
      
    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://util.profiling.unittest.TestCase#assertEquals
     * @access  protected
     * @param   string expect
     * @param   string actual
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertXmlEquals($expect, $actual) {
      return $this->assertEquals(
        $this->xmlDeclaration.preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($actual))
      );
    }

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @model   abstract
     * @access  protected
     * @return  string
     */
    function neededExtension() { }

    /**
     * Returns the XSL processor instance to be used
     *
     * @model   abstract
     * @access  protected
     * @return  &xml.IXSLProcessor
     */
    function &processorInstance() { }

    /**
     * Returns the XSL processor's default output charset
     *
     * @model   abstract
     * @access  protected
     * @return  string
     */
    function processorCharset() { }

    /**
     * Tests 
     *
     * @access  public
     * @throws  util.profiling.unittest.PrerequisitesNotMetError
     */
    function setUp() {
      if (!extension_loaded($ext= $this->neededExtension())) {
        return throw(new PrerequisitesNotMetError($ext.' extension not loaded'));
      }
      $this->processor= &$this->processorInstance();
      $this->xmlDeclaration= '<?xml version="1.0" encoding="'.$this->processorCharset().'"?>';
    }

    /**
     * Tests the setParam() and getParam() methods
     *
     * @access  public
     */
    #[@test]
    function paramAccessors() {
      $this->processor->setParam('a', 'b');
      $this->assertEquals('b', $this->processor->getParam('a'));
    }

    /**
     * Tests the setBase() and getBase() methods
     *
     * @access  public
     */
    #[@test]
    function baseAccessors() {
      $path= rtrim(realpath('../xml/'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
      $this->processor->setBase($path);
      $this->assertEquals($path, $this->processor->getBase());
    }

    /**
     * Tests the setBase() adds trailing DIRECTORY_SEPARATOR
     *
     * @access  public
     */
    #[@test]
    function setBaseAddsTrailingDirectorySeparator() {
      $path= rtrim(realpath('../xml/'), DIRECTORY_SEPARATOR);
      $this->processor->setBase($path);
      $this->assertEquals($path.DIRECTORY_SEPARATOR, $this->processor->getBase());
    }

    /**
     * Tests the setParams() methods
     *
     * @access  public
     */
    #[@test]
    function setParams() {
      $this->processor->setParams(array(
        'a'     => 'b',
        'left'  => 'one',
        'right' => 'two'
      ));
      $this->assertEquals('b', $this->processor->getParam('a')) &&
      $this->assertEquals('one', $this->processor->getParam('left')) &&
      $this->assertEquals('two', $this->processor->getParam('right'));
    }

    /**
     * Tests a transformation that will result in an empty result
     *
     * @access  public
     */
    #[@test]
    function transformationWithEmptyResult() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('', $this->processor->output());
    }

    /**
     * Tests a transformation
     *
     * @access  public
     */
    #[@test]
    function transformationWithResult() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/">
            <b>Hello</b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertXmlEquals('<b>Hello</b>', $this->processor->output());
    }

    /**
     * Tests a transformation with parameters
     *
     * @access  public
     */
    #[@test]
    function transformationWithParameter() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:param name="input"/>
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/">
            <b><xsl:value-of select="$input"/></b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->setParam('input', 'Parameter #1');
      $this->processor->run();
      $this->assertXmlEquals('<b>Parameter #1</b>', $this->processor->output());
    }

    /**
     * Tests a transformation with parameters
     *
     * @access  public
     */
    #[@test]
    function transformationWithParameters() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:param name="left"/>
          <xsl:param name="right"/>
          <xsl:output method="xml" encoding="utf-8"/>
          <xsl:template match="/">
            <b><xsl:value-of select="$left + $right"/></b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->setParams(array(
        'left'  => '1',
        'right' => '2',
      ));
      $this->processor->run();
      $this->assertXmlEquals('<b>3</b>', $this->processor->output());
    }

    /**
     * Tests a transformation with malformed XML
     *
     * @access  public
     */
    #[@test, @expect('xml.TransformerException')]
    function malformedXML() {
      $this->processor->setXMLBuf('@@MALFORMED@@');
      $this->processor->setXSLBuf('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
      $this->processor->run();
    }

    /**
     * Tests a transformation with malformed XSL
     *
     * @access  public
     */
    #[@test, @expect('xml.TransformerException')]
    function malformedXSL() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('@@MALFORMED@@');
      $this->processor->run();
    }
  }
?>
