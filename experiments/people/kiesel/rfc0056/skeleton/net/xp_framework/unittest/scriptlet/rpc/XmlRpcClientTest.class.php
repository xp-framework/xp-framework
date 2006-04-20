<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'xml.xmlrpc.XmlRpcClient',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyRpcTransport'
  );

  /**
   * Testcase for XmlRpcClient
   *
   * @see      xp://xml.xmlrpc.XmlRpcClient
   * @purpose  TestCase
   */
  class XmlRpcClientTest extends TestCase {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function simpleRequest() {
      $client= &new XmlRpcClient(new DummyRpcTransport('http://localhost:12345/'));
      $conn= &$client->transport->getConnection();
      $conn->request->setResponse('HTTP/1.0 200 OK

Foo');
      var_dump($client->invoke('foo'));
    }
  }
?>
