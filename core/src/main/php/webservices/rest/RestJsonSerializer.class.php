<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.RestSerializer', 'webservices.json.JsonFactory');

  /**
   * A JSON serializer
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestJsonSerializerTest
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  class RestJsonSerializer extends RestSerializer {

    /**
     * Return the Content-Type header's value
     *
     * @return  string
     */
    public function contentType() {
      return 'application/json; charset=utf-8';
    }
    
    /**
     * Serialize
     *
     * @param   var value
     * @return  string
     */
    public function serialize($payload) {
      $encoder= JsonFactory::create();
      if ($payload instanceof Payload) {
        return $encoder->encode($this->convert($payload->value));
      } else {
        return $encoder->encode($this->convert($payload));
      }
    }
  }
?>
