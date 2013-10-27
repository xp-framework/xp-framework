<?php namespace net\xp_framework\unittest\peer\http;

/**
 * Mock HTTP connection
 *
 * @see   xp://peer.http.HttpConnection
 */
class MockHttpConnection extends \peer\http\HttpConnection {
  protected $lastRequest= null;
  protected $cat= null;

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
  public function send(\peer\http\HttpRequest $request) {
    $this->lastRequest= $request;

    $this->cat && $this->cat->info('>>>', $request->getHeaderString());
    $response= new \peer\http\HttpResponse(
      new \io\streams\MemoryInputStream("HTTP/1.0 200 Testing OK\r\n")
    );
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
