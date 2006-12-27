<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpConnection',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyHttpRequest'
  );

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
?>
