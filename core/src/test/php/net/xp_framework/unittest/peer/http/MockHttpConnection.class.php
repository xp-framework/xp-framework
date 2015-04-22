<?php namespace net\xp_framework\unittest\peer\http;

use peer\http\HttpRequest;
use peer\http\HttpResponse;

/**
 * Mock HTTP connection
 *
 * @see   xp://peer.http.HttpConnection
 */
class MockHttpConnection extends \peer\http\HttpConnection {
  protected $lastRequest= null;
  protected $response= null;
  protected $cat= null;

  /** @return string */
  public function lastRequest() { return $this->lastRequest->getRequestString(); }

  public function setResponse(HttpResponse $response) {
    $this->response= $response;
  }

  protected function response() {
    if ($this->response) {
      $r= $this->response;
      $this->response= null;
      return $r;
    }

    return new HttpResponse(new \io\streams\MemoryInputStream("HTTP/1.0 200 Testing OK\r\n"));
  }

  /**
   * Send a HTTP request
   *
   * @param   peer.http.HttpRequest
   * @return  peer.http.HttpResponse response object
   */
  public function send(HttpRequest $request) {
    $this->lastRequest= $request;

    $this->cat && $this->cat->info('>>>', $request->getHeaderString());
    $response= $this->response();
    $this->cat && $this->cat->info('<<<', $response->getHeaderString());
    return $response;
  }

  /**
   * Sets a logger category for debugging
   *
   * @param   util.log.LogCategory $cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
}
