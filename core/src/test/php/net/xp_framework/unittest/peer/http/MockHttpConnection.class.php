<?php namespace net\xp_framework\unittest\peer\http;

use peer\http\HttpConnection;


/**
 * Mock HTTP connection
 *
 * @test     xp://peer.http.HttpConnection
 * @purpose  Mock connection
 */
class MockHttpConnection extends HttpConnection {
  var
    $lastRequest= null;

  /**
   * Returns last request
   *
   * @return peer.http.HttpRequest
   */
  public function getLastRequest() {
    return $this->lastRequest;
  }

  /**
   * Send a HTTP request
   *
   * @param   peer.http.HttpRequest
   * @return  peer.http.HttpResponse response object
   */
  public function send(\peer\http\HttpRequest $r) {
    $this->lastRequest= $r;
  }
}
