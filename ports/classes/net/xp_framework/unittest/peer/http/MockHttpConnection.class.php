<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.http.HttpConnection');
  
  /**
   * Mock HTTP connection
   *
   * @test     xp://peer.http.HttpConnection
   * @purpose  Mock connection
   */
  class MockHttpConnection extends HttpConnection {
    var
      $lastRequest= NULL;
  
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
    public function send(HttpRequest $r) {
      $this->lastRequest= $r;
    }
  }
?>
