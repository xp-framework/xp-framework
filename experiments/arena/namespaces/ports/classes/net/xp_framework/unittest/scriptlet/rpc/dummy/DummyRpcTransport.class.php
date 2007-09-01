<?php
/* This class is part of the XP framework
 *
 * $Id: DummyRpcTransport.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc::dummy;

  ::uses(
    'webservices.xmlrpc.transport.XmlRpcHttpTransport',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyHttpConnection'
  );

  /**
   * Dummy Transport
   *
   * @purpose  Unittesting dummy
   */
  class DummyRpcTransport extends webservices::xmlrpc::transport::XmlRpcHttpTransport {
  
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
