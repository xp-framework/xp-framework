<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.RestSerializer', 'webservices.json.JsonFactory');

  /**
   * A JSON serializer
   *
   * @see   xp://webservices.rest.RestSerializer
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestJsonSerializerTest
   */
  class RestJsonSerializer extends RestSerializer {
    protected $json;

    /**
     * Constructor. Initializes decoder member
     */
    public function __construct() {
      $this->json= JsonFactory::create();
    }

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
      if ($payload instanceof Payload) {
        return $this->json->encode($payload->value);
      } else {
        return $this->json->encode($payload);
      }
    }
  }
?>
