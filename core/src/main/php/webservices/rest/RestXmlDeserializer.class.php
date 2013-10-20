<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.rest.RestDeserializer',
    'xml.Tree',
    'xml.parser.XMLParser',
    'xml.parser.StreamInputSource',
    'webservices.rest.RestXmlMap'
  );

  /**
   * An XML deserializer
   *
   * @see   xp://webservices.rest.RestDeserializer
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestXmlDeserializerTest
   */
  class RestXmlDeserializer extends RestDeserializer {

    /**
     * Deserialize
     *
     * @param   io.streams.InputStream in
     * @return  var
     * @throws  lang.FormatException
     */
    public function deserialize($in) {
      $tree= new Tree();
      create(new XMLParser())->withCallback($tree)->parse(new StreamInputSource($in));
      return new RestXmlMap($tree->root);
    }
  }
?>
