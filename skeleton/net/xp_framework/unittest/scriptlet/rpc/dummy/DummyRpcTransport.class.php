<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    //'scriptlet.rpc.transport.GenericHttpTransport',
    'xml.xmlrpc.transport.XmlRpcHttpTransport',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyHttpConnection'
  );

  /**
   * Dummy Transport
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class DummyRpcTransport extends XmlRpcHttpTransport {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($url, $headers= array()) {
      $this->_conn= &new DummyHttpConnection($url);
      $this->_headers= $headers;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getConnection() {
      return $this->_conn;
    }
  }
?>
