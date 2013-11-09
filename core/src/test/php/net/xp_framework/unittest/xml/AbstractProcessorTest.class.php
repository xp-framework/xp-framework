<?php namespace net\xp_framework\unittest\xml;
 
/**
 * Test XSL processor
 *
 * @see    xp://xml.IXSLProcessor
 */
class AbstractProcessorTest extends \unittest\TestCase {
  public $processor= null;
  public $xmlDeclaration= '';
    
  /**
   * Compares XML after stripping all whitespace between tags of both 
   * expected and actual strings.
   *
   * @see     xp://unittest.TestCase#assertEquals
   * @param   string $expect
   * @param   string $actual
   * @throws  unittest.AssertionFailedError
   */
  public function assertXmlEquals($expect, $actual) {
    $this->assertEquals(
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
    
    // Normalize URI according to http://en.wikipedia.org/wiki/File_URI_scheme
    // * "f:\a dir\c.xsl"       => "file:///f:/a%20dor/c.xsl"
    // * "/a dir/c.xsl"         => "file:///a%20dir/c.xsl"
    // * "xar://f:\a.xar?c.xsl" => "xar:///f:/a.xar;c.csl"
    // * "xar:///a.xar?c.xsl"   => "xar:///a.xar;c.csl"
    if (false === ($p= strpos($name, '://'))) {
      $scheme= 'file';
    } else {
      $scheme= substr($name, 0, $p);
      $name= substr($name, $p+ 3);
    }
    if (':' === $name{1}) {
      $name= '/'.$name;
    }
    return $scheme.'://'.strtr($name, array(DIRECTORY_SEPARATOR => '/', ' ' => '%20', '?' => ';'));
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
        throw new \unittest\PrerequisitesNotMetError($ext.' extension not loaded');
      }
    }
    $this->processor= $this->processorInstance();
    $this->xmlDeclaration= '<?xml version="1.0" encoding="'.$this->processorCharset().'"?>';
  }

  #[@test, @expect('io.FileNotFoundException')]
  public function setNonExistantXMLFile() {
    $this->processor->setXMLFile(':does-no-exist:');
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXMLFile() {
    $this->processor->setXMLFile($this->includeUri('malformed'));
  }

  #[@test]
  public function setXMLFile() {
    $this->processor->setXMLFile($this->includeUri('include'));
  }

  #[@test]
  public function setXMLBuf() {
    $this->processor->setXMLBuf('<document/>');
  }

  #[@test]
  public function setXMLTree() {
    $this->processor->setXMLTree(new \xml\Tree('document'));
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXMLTree() {
    $this->processor->setXMLTree(new \xml\Tree('<!>'));    // xml.Tree does not check this!
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXMLBuf() {
    $this->processor->setXMLBuf('this-is-not-valid<XML>');
  }

  #[@test, @expect('io.FileNotFoundException')]
  public function setNonExistantXSLFile() {
    $this->processor->setXSLFile(':does-no-exist:');
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXSLFile() {
    $this->processor->setXSLFile($this->includeUri('malformed'));
  }

  #[@test]
  public function setXSLFile() {
    $this->processor->setXSLFile($this->includeUri('include'));
  }

  #[@test]
  public function setXSLBuf() {
    $this->processor->setXSLBuf('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXSLBuf() {
    $this->processor->setXSLBuf('<xsl stylsheet!');
  }

  #[@test]
  public function setXSLTree() {
    $t= new \xml\Tree('xsl:stylesheet');
    $t->root()->setAttribute('xmlns:xsl', 'http://www.w3.org/1999/XSL/Transform');
    $this->processor->setXSLTree($t);
  }

  #[@test, @expect('xml.TransformerException')]
  public function setMalformedXSLTree() {
    $this->processor->setXSLTree(new \xml\Tree('<!>'));    // xml.Tree does not check this!
  }

  #[@test]
  public function paramAccessors() {
    $this->processor->setParam('a', 'b');
    $this->assertEquals('b', $this->processor->getParam('a'));
  }

  #[@test]
  public function baseAccessors() {
    $file= \lang\Runtime::getInstance()->getExecutable()->getFilename();
    $path= rtrim(realpath(dirname($file)), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    $this->processor->setBase($path);
    $this->assertEquals($path, $this->processor->getBase());
  }

  #[@test]
  public function setBaseAddsTrailingDirectorySeparator() {
    $file= \lang\Runtime::getInstance()->getExecutable()->getFilename();
    $path= rtrim(realpath(dirname($file)), DIRECTORY_SEPARATOR);
    $this->processor->setBase($path);
    $this->assertEquals($path.DIRECTORY_SEPARATOR, $this->processor->getBase());
  }

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

  #[@test, @expect('xml.TransformerException')]
  public function malformedXML() {
    $this->processor->setXMLBuf('@@MALFORMED@@');
    $this->processor->setXSLBuf('<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"/>');
    $this->processor->run();
  }

  #[@test, @expect('xml.TransformerException')]
  public function malformedXSL() {
    $this->processor->setXMLBuf('<document/>');
    $this->processor->setXSLBuf('@@MALFORMED@@');
    $this->processor->run();
  }

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
