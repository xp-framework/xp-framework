<?php namespace net\xp_framework\unittest\xml;

/**
 * Tests XML parser API with primitive string source
 *
 * @see  xp://net.xp_framework.unittest.xml.AbstractXMLParserTest
 */
class StringXMLParserTest extends AbstractXMLParserTest {
  
  /**
   * Returns an XML document by prepending the XML declaration to 
   * the given string and returning it.
   *
   * @param   string $str
   * @param   bool $decl default TRUE
   * @return  xml.parser.InputSource XML the source XML
   */
  protected function source($str, $decl= true) {
    return new \xml\parser\StringInputSource(
      ($decl ? '<?xml version="1.0" encoding="utf-8"?>' : '').$str,
      $this->name.' test'
    );
  }
}
