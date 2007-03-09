<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xml.DomXSLProcessor'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class XslTestCase extends TestCase {

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      $this->processor= new DomXSLProcessor();
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @param   
     * @param   
     */
    protected function assertTransformation($expected, $xsl, $xml) {
      $this->processor->setXSLBuf($xsl);
      $this->processor->setXMLBuf($xml);
      $this->processor->run();
      $this->assertEquals($expected, $this->processor->output());
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function testSelf() {
      $this->assertTransformation(
        '<?xml version="1.0" encoding="iso-8859-1"?>
<document/>
', 
        '<?xml version="1.0"?>
<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
  extension-element-prefixes="func"
>
  <xsl:output method="xml" encoding="iso-8859-1"/>
  
  <xsl:template match="/">
    <document/>
  </xsl:template>
</xsl:stylesheet>
        ',
        '<document/>'
      );
    }
  }
?>
