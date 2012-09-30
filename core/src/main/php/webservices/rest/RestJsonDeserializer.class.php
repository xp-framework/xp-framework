<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.rest.RestDeserializer',
    'webservices.json.JsonFactory'
  );

  /**
   * A JSON deserializer
   *
   */
  class RestJsonDeserializer extends RestDeserializer {

    /**
     * Serialize
     *
     * @param   io.streams.InputStream in
     * @param   lang.Type target
     * @return  var
     */
    public function deserialize($in, $target) {
      return $this->convert($target, JsonFactory::create()->decodeFrom($in));
    }
  }
?>
