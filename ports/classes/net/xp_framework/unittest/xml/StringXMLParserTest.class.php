<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.xml.AbstractXMLParserTest');

  /**
   * TestCase
   *
   * @see      xp://net.xp_framework.unittest.xml.AbstractXMLParserTest
   */
  class StringXMLParserTest extends AbstractXMLParserTest {
    
    /**
     * Returns an XML document by prepending the XML declaration to 
     * the given string and returning it.
     *
     * @param   string str
     * @param   bool decl default TRUE
     * @return  * XML the source XML
     */
    protected function source($str, $decl= TRUE) {
      return ($decl ? '<?xml version="1.0" encoding="utf-8"?>' : '').$str;
    }
  }
?>
