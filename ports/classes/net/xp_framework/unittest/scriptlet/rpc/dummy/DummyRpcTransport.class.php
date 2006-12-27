<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.xmlrpc.transport.XmlRpcHttpTransport',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyHttpConnection'
  );

  /**
   * Dummy Transport
   *
   * @purpose  Unittesting dummy
   */
  class DummyRpcTransport extends XmlRpcHttpTransport {
  
    /**
     * Constructor
     *
     * @param   string url
     * @param   array headers default array
     */
    public function __construct($url, $headers= array()) {
      $this->_conn= new DummyHttpConnection($url);
      $this->_headers= $headers;
    }
    
    /**
     * Retrieve connection
     *
     * @return  &peer.http.HttpConnection
     */
    public function getConnection() {
      return $this->_conn;
    }
  }
?>
