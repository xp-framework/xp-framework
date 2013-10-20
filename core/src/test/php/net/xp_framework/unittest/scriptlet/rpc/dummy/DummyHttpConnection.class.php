<?php namespace net\xp_framework\unittest\scriptlet\rpc\dummy;

use peer\http\HttpConnection;


/**
 * Dummy HTTP connection
 *
 * @purpose  Unittesting dummy
 */
class DummyHttpConnection extends HttpConnection {

  /**
   * Create request
   *
   * @param   &peer.URL url
   */
  protected function _createRequest($url) {
    $this->request= new DummyHttpRequest($url);
  }
}
