<?php namespace net\xp_framework\unittest\webservices\rest;

use webservices\rest\RestResponse;
use webservices\rest\RestException;

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
      $e= $this->reader->read(\lang\Type::$VAR, $this->input);
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
    if (204 === $this->response->statusCode()) {
      return null;  // "No Content"
    } else {
      return parent::handlePayloadOf($target);
    }
  }
}
