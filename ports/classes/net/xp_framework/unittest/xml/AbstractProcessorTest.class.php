<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase');

  /**
   * Test XSL processor
   *
   * @see      xp://xml.IXSLProcessor
   * @purpose  Unit Test
   */
  class AbstractProcessorTest extends TestCase {
    public
      $processor      = NULL,
      $xmlDeclaration = '';
      
    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://unittest.TestCase#assertEquals
     * @param   string expect
     * @param   string actual
     * @throws  unittest.AssertionFailedError
     */
    public function assertXmlEquals($expect, $actual) {
      return $this->assertEquals(
        $this->xmlDeclaration.preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($actual))
      );
    }
    
    /**
     * Gets the include URI
     *
     * @param   string stylesheet name (w/o .xsl extension) of a XSL file in the same directory as this class
     * @return  string
     */
    protected function includeUri($stylesheet) {
      $name= $this->getClass()->getPackage()->getResourceAsStream($stylesheet.'.xsl')->getURI();
      
      // FIXME: workaround for "xsl:include : invalid URI reference C:\cygwin\...\include.xsl"
      // oddity in PHP's XSL libraries (needs forward-slash even on Windows)
      return strstr($name, '://') ? $name : 'file:///'.strtr($name, array(DIRECTORY_SEPARATOR => '/', ' ' => '%20'));
    }

    /**
     * Returns the PHP extension needed for this processor test to work
     *
     * @return  string
     */
    public function neededExtension() { }

    /**
     * Returns the XSL processor instance to be used
     *
     * @return  xml.IXSLProcessor
     */
    public function processorInstance() { }

    /**
     * Returns the XSL processor's default output charset
     *
     * @return  string
     */
    public function processorCharset() { }

    /**
     * Tests 
     *
     * @throws  unittest.PrerequisitesNotMetError
     */
    public function setUp() {
      foreach ((array)$this->neededExtension() as $ext) {
        if (!extension_loaded($ext)) {
          throw new PrerequisitesNotMetError($ext.' extension not loaded');
        }
      }
      $this->processor= $this->processorInstance();
      $this->xmlDeclaration= '<?xml version="1.0" encoding="'.$this->processorCharset().'"?>';
    }

    /**
     * Tests setXMLFile() method
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function setNonExistantXMLFile() {
      $this->processor->setXMLFile(':does-no-exist:');
    }

    /**
     * Tests setXMLFile() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXMLFile() {
      $this->processor->setXMLFile($this->includeUri('malformed'));
    }

    /**
     * Tests setXMLFile() method
     *
     */
    #[@test]
    public function setXMLFile() {
      $this->processor->setXMLFile($this->includeUri('include'));
    }

    /**
     * Tests setXMLBuf() method
     *
     */
    #[@test]
    public function setXMLBuf() {
      $this->processor->setXMLBuf('<document/>');
    }

    /**
     * Tests setXMLTree() method
     *
     */
    #[@test]
    public function setXMLTree() {
      $this->processor->setXMLTree(new Tree('document'));
    }

    /**
     * Tests setXMLTree() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXMLTree() {
      $this->processor->setXMLTree(new Tree('<!>'));    // xml.Tree does not check this!
    }

    /**
     * Tests setXMLBuf() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXMLBuf() {
      $this->processor->setXMLBuf('this-is-not-valid<XML>');
    }

    /**
     * Tests setXSLFile() method
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function setNonExistantXSLFile() {
      $this->processor->setXSLFile(':does-no-exist:');
    }

    /**
     * Tests setXSLFile() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXSLFile() {
      $this->processor->setXSLFile($this->includeUri('malformed'));
    }

    /**
     * Tests setXSLFile() method
     *
     */
    #[@test]
    public function setXSLFile() {
      $this->processor->setXSLFile($this->includeUri('include'));
    }

    /**
     * Tests setXSLBuf() method
     *
     */
    #[@test]
    public function setXSLBuf() {
      $this->processor->setXSLBuf('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
    }

    /**
     * Tests setXSLBuf() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXSLBuf() {
      $this->processor->setXSLBuf('<xsl stylsheet!');
    }

    /**
     * Tests setXSLTree() method
     *
     */
    #[@test]
    public function setXSLTree() {
      $t= new Tree('xsl:stylesheet');
      $t->root->setAttribute('xmlns:xsl', 'http://www.w3.org/1999/XSL/Transform');
      $this->processor->setXSLTree($t);
    }

    /**
     * Tests setXSLTree() method
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function setMalformedXSLTree() {
      $this->processor->setXSLTree(new Tree('<!>'));    // xml.Tree does not check this!
    }

    /**
     * Tests the setParam() and getParam() methods
     *
     */
    #[@test]
    public function paramAccessors() {
      $this->processor->setParam('a', 'b');
      $this->assertEquals('b', $this->processor->getParam('a'));
    }

    /**
     * Tests the setBase() and getBase() methods
     *
     */
    #[@test]
    public function baseAccessors() {
      $path= rtrim(realpath(dirname(__FILE__).'/../xml/'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
      $this->processor->setBase($path);
      $this->assertEquals($path, $this->processor->getBase());
    }

    /**
     * Tests the setBase() adds trailing DIRECTORY_SEPARATOR
     *
     */
    #[@test]
    public function setBaseAddsTrailingDirectorySeparator() {
      $path= rtrim(realpath(dirname(__FILE__).'/../xml/'), DIRECTORY_SEPARATOR);
      $this->processor->setBase($path);
      $this->assertEquals($path.DIRECTORY_SEPARATOR, $this->processor->getBase());
    }

    /**
     * Tests the setParams() methods
     *
     */
    #[@test]
    public function setParams() {
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
     */
    #[@test]
    public function transformationWithEmptyResult() {
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
     * Tests a transformation w/ ISO-8859-1 XSL without output encoding
     * results in the default processor charset.
     *
     */
    #[@test]
    public function iso88591XslWithoutOutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="iso-8859-1"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text"/>
          <xsl:template match="/">
            <xsl:text>Hällo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals($this->processorCharset(), $this->processor->outputEncoding());
      $this->assertEquals(
        iconv('iso-8859-1', $this->processorCharset(), 'Hällo'), 
        $this->processor->output()
      );
    }

    /**
     * Tests a transformation w/ ISO-8859-1 XSL with utf-8 output encoding
     * results in UTF-8 output.
     *
     */
    #[@test]
    public function iso88591XslWithUtf8OutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="iso-8859-1"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text" encoding="utf-8"/>
          <xsl:template match="/">
            <xsl:text>Hällo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('utf-8', $this->processor->outputEncoding());
      $this->assertEquals('HÃ¤llo', $this->processor->output());
    }

    /**
     * Tests a transformation w/ UTF-8 XSL without output encoding
     * results in the default processor charset.
     *
     */
    #[@test]
    public function utf8XslWithoutOutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="utf-8"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text"/>
          <xsl:template match="/">
            <xsl:text>HÃ¤llo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals($this->processorCharset(), $this->processor->outputEncoding());
      $this->assertEquals(
        iconv('iso-8859-1', $this->processorCharset(), 'Hällo'), 
        $this->processor->output()
      );
    }

    /**
     * Tests a transformation w/ UTF-8 XSL with utf-8 output encoding
     * results in UTF-8 output.
     *
     */
    #[@test]
    public function utf8XslWithUtf8OutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="utf-8"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text" encoding="utf-8"/>
          <xsl:template match="/">
            <xsl:text>HÃ¤llo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('utf-8', $this->processor->outputEncoding());
      $this->assertEquals('HÃ¤llo', $this->processor->output());
    }

    /**
     * Tests a transformation w/ UTF-8 XSL with iso-8859-1 output encoding
     * results in iso-8859-1 output.
     *
     */
    #[@test]
    public function utf8XslWithIso88591OutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="utf-8"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text" encoding="iso-8859-1"/>
          <xsl:template match="/">
            <xsl:text>HÃ¤llo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
      $this->assertEquals('Hällo', $this->processor->output());
    }

    /**
     * Tests a transformation w/ ISO-8859-1 XSL with iso-8859-1 output encoding
     * results in iso-8859-1 output.
     *
     */
    #[@test]
    public function iso88591XslWithIso88591OutputEncoding() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('<?xml version="1.0" encoding="iso-8859-1"?>
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="text" encoding="iso-8859-1"/>
          <xsl:template match="/">
            <xsl:text>Hällo</xsl:text>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
      $this->assertEquals('Hällo', $this->processor->output());
    }

    /**
     * Tests a transformation
     *
     */
    #[@test]
    public function transformationWithResult() {
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
     * Tests a transformation to HTML
     *
     */
    #[@test]
    public function transformationToHtml() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="html" encoding="utf-8"/>
          <xsl:template match="/">
            <b>Hello</b>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('<b>Hello</b>', trim($this->processor->output()));
    }

    /**
     * Tests a transformation javascript embedded in a CDATA section
     *
     */
    #[@test]
    public function javaScriptInCDataSection() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="html" encoding="utf-8"/>
          <xsl:template match="/">
            <script language="JavaScript"><![CDATA[ alert(1 && 2); ]]></script>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals(
        '<script language="JavaScript"> alert(1 && 2); </script>', 
        trim($this->processor->output())
      );
    }

    /**
     * Tests a transformation with parameters
     *
     */
    #[@test]
    public function omitXmlDeclaration() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:output method="xml" encoding="utf-8" omit-xml-declaration="yes"/>
          <xsl:template match="/">
            <tag>No XML declaration</tag>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('<tag>No XML declaration</tag>', trim($this->processor->output()));
    }

    /**
     * Tests a transformation with parameters
     *
     */
    #[@test]
    public function transformationWithParameter() {
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
     */
    #[@test]
    public function transformationWithParameters() {
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
     */
    #[@test, @expect('xml.TransformerException')]
    public function malformedXML() {
      $this->processor->setXMLBuf('@@MALFORMED@@');
      $this->processor->setXSLBuf('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
      $this->processor->run();
    }

    /**
     * Tests a transformation with malformed XSL
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function malformedXSL() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('@@MALFORMED@@');
      $this->processor->run();
    }

    /**
     * Tests a transformation with malformed XSL expression
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function malformedExpression() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:template match="/">
            <xsl:value-of select="concat(\'Hello\', "/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }

    /**
     * Tests a transformation with an unbound variable
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function unboundVariable() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:template match="/">
            <xsl:value-of select="$a"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }

    /**
     * Tests a transformation with an xsl:include reference to a non-
     * existant file.
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function includeNotFound() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:include href=":@@FILE-DOES-NOT-EXIST@@:"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }

    /**
     * Tests a transformation with an xsl:import reference to a non-
     * existant file.
     *
     */
    #[@test, @expect('xml.TransformerException')]
    public function importNotFound() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:import href=":@@FILE-DOES-NOT-EXIST@@:"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
    }

    /**
     * Tests a transformation with an xsl:include reference to an
     * existant file.
     *
     */
    #[@test]
    public function includingAFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:include href="'.$this->includeUri('include').'"/>
          <xsl:template match="/">
            <xsl:value-of select="$a"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('TEST', $this->processor->output());
    }

    /**
     * Tests a transformation with an xsl:import reference to an
     * existant file.
     *
     */
    #[@test]
    public function importingAFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:import href="'.$this->includeUri('include').'"/>
          <xsl:template match="/">
            <xsl:value-of select="$a"/>
          </xsl:template>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('TEST', $this->processor->output());
    }

    /**
     * Tests the output-encoding is determined from the included file
     *
     */
    #[@test]
    public function outputEncodingFromIncludedFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:include href="'.$this->includeUri('include').'"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
    }

    /**
     * Tests the output-encoding is determined from the imported file
     *
     */
    #[@test]
    public function outputEncodingFromImportedFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:import href="'.$this->includeUri('include').'"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
    }

    /**
     * Tests the output-encoding is determined from the file included 
     * from an imported file.
     *
     */
    #[@test]
    public function outputEncodingFromIncludedInImportedFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:import href="'.$this->includeUri('includer').'"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
    }

    /**
     * Tests the output-encoding is determined from the file included 
     * from an included file.
     *
     */
    #[@test]
    public function outputEncodingFromIncludedInIncludedFile() {
      $this->processor->setXMLBuf('<document/>');
      $this->processor->setXSLBuf('
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
          <xsl:include href="'.$this->includeUri('includer').'"/>
        </xsl:stylesheet>
      ');
      $this->processor->run();
      $this->assertEquals('iso-8859-1', $this->processor->outputEncoding());
    }
  }
?>
