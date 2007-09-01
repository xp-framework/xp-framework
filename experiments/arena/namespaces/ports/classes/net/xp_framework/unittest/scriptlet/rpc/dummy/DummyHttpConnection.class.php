<?php
/* This class is part of the XP framework
 *
 * $Id: DummyHttpConnection.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc::dummy;

  ::uses(
    'peer.http.HttpConnection',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyHttpRequest'
  );

  /**
   * Dummy HTTP connection
   *
   * @purpose  Unittesting dummy
   */
  class DummyHttpConnection extends peer::http::HttpConnection {
  
    /**
     * Create request
     *
     * @param   &peer.URL url
     */
    protected function _createRequest($url) {
      $this->request= new DummyHttpRequest($url);
    }
  }
?>
