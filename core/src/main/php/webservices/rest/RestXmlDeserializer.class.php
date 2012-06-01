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
   */
  class RestXmlDeserializer extends RestDeserializer {

    /**
     * Deserialize
     *
     * @param   io.streams.InputStream in
     * @param   lang.Type target
     * @return  var
     */
    public function deserialize($in, $target) {
      $tree= new Tree();
      create(new XMLParser())->withCallback($tree)->parse(new StreamInputSource($in));
      return $this->convert($target, new RestXmlMap($tree->root));
    }
  }
?>
