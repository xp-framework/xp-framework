<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.rest.RestResponse', 
    'net.xp_framework.unittest.webservices.rest.CustomRestException'
  );
  
  /**
   * Fixture for CustomRestResponseTest
   *
   * @see   xp://webservices.rest.RestResponse
   */
  class CustomRestResponse extends RestResponse {

    /**
     * Handle status
     *
     * @param   int code
     * @throws  webservices.rest.RestException
     */
    protected function handleStatus($code) {
      if ($code > 399) {
        // TODO: $type= this($this->response->header('Content-Type'), 0);

        $e= $this->deserializer->deserialize($this->input, Type::forName('[:var]'));
        throw new CustomRestException($e, new RestException($code.': '.$this->response->message()));
      }
    }

    /**
     * Handle payload
     * 
     * @param   lang.Type target
     * @return  var
     */
    protected function handlePayloadOf($target) {
      if (204 === $this->response->statusCode()) return NULL;  // "No Content"

      return parent::handlePayloadOf($target);
    }
  }
?>
