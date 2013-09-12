<?php namespace net\xp_framework\unittest\webservices\rest;

use webservices\rest\RestResponse;


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

      $e= $this->deserializer->deserialize($this->input, \lang\Type::forName('[:var]'));
      throw new CustomRestException($e, new \webservices\rest\RestException($code.': '.$this->response->message()));
    }
  }

  /**
   * Handle payload
   * 
   * @param   lang.Type target
   * @return  var
   */
  protected function handlePayloadOf($target) {
    if (204 === $this->response->statusCode()) return null;  // "No Content"

    return parent::handlePayloadOf($target);
  }
}
