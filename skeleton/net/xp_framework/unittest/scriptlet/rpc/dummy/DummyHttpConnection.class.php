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
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class DummyHttpConnection extends HttpConnection {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _createRequest(&$url) {
      $this->request= &new DummyHttpRequest($url);
    }
  }
?>
