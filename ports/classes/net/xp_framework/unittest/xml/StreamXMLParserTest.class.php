<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.xml.AbstractXMLParserTest', 
    'xml.parser.StreamInputSource',
    'io.streams.MemoryInputStream'
  );

  /**
   * Tests XML parser API with io.streams.InputStream source
   *
   * @see      xp://net.xp_framework.unittest.xml.AbstractXMLParserTest
   */
  class StreamXMLParserTest extends AbstractXMLParserTest {
    
    /**
     * Returns an XML document by prepending the XML declaration to 
     * the given string and returning it.
     *
     * @param   string str
     * @param   bool decl default TRUE
     * @return  xml.parser.InputSource XML the source XML
     */
    protected function source($str, $decl= TRUE) {
      return new StreamInputSource(
        new MemoryInputStream(($decl ? '<?xml version="1.0" encoding="utf-8"?>' : '').$str),
        $this->name.' test'
      );
    }
  }
?>
