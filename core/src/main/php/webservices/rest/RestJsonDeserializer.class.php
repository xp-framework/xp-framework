<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.RestDeserializer',  'webservices.json.JsonFactory');

  /**
   * A JSON deserializer
   *
   * @see   xp://webservices.rest.RestDeserializer
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestJsonDeserializerTest
   */
  class RestJsonDeserializer extends RestDeserializer {

    /**
     * Serialize
     *
     * @param   io.streams.InputStream in
     * @return  [:var]
     * @throws  lang.FormatException
     */
    public function deserialize($in) {
      try {
        return JsonFactory::create()->decodeFrom($in);
      } catch (JsonException $e) {
        throw new FormatException('Malformed JSON', $e);
      }
    }
  }
?>
