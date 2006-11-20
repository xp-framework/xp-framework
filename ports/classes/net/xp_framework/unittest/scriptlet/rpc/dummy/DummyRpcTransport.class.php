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
     * @access  public
     * @param   string url
     * @param   array headers default array
     */
    function __construct($url, $headers= array()) {
      $this->_conn= &new DummyHttpConnection($url);
      $this->_headers= $headers;
    }
    
    /**
     * Retrieve connection
     *
     * @access  public
     * @return  &peer.http.HttpConnection
     */
    function &getConnection() {
      return $this->_conn;
    }
  }
?>
